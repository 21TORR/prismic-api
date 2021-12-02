<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Helper\KeyedMapHelper;
use Torr\PrismicApi\Transform\FieldValueTransformer;

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

	/**
	 * @inheritDoc
	 */
	public function transformValue (mixed $data, FieldValueTransformer $valueTransformer) : mixed
	{
		$result = [];

		foreach ($data as $entry)
		{
			$transformed = [];

			foreach ($this->fields as $key => $field)
			{
				$transformed[$key] = $field->transformValue(
					$entry[$key] ?? null,
					$valueTransformer,
				);
			}

			$result[] = $transformed;
		}

		return $result;
	}
}
