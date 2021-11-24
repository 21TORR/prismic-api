<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\CustomType\Helper\FilterFieldsHelper;
use Torr\PrismicApi\CustomType\Helper\KeyedMapHelper;

/**
 * @see https://prismic.io/docs/core-concepts/group
 */
final class GroupField extends InputField
{
	private const TYPE_KEY = "Group";


	/**
	 * @inheritDoc
	 *
	 * @param array<string, InputField> $fields
	 */
	public function __construct (
		string $label,
		private array $fields,
		?bool $repeat = false,
		private bool $required = false,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"repeat" => $repeat,
			"fields" => KeyedMapHelper::transformKeyedListOfTypes($this->fields),
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function getValidationConstraints () : array
	{
		$fields = [];

		foreach ($this->fields as $key => $field)
		{
			$fields[$key] = $field->getValidationConstraints();
		}

		$constraints = [
			new Assert\Type("array"),
			new Assert\All([
				"constraints" => [
					new Assert\Collection([
						"fields" => $fields,
						"allowExtraFields" => true,
						"allowMissingFields" => false,
					]),
				],
			]),
		];

		if ($this->required)
		{
			$constraints[] = new Assert\NotNull();
			$constraints[] = new Assert\Count(min: 1);
		}

		return $constraints;
	}
}
