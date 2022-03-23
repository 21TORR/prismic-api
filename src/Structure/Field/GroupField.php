<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Helper\KeyedMapHelper;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;
use Torr\PrismicApi\Transform\FieldValueTransformer;

/**
 * @see https://prismic.io/docs/core-concepts/group
 */
final class GroupField extends InputField
{
	use ValueValidationTrait;
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
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		// validate field itself
		$this->ensureDataIsValid($validator, $data, [
			new Assert\Type("array"),
			new Assert\All([
				"constraints" => [
					new Assert\Collection([
						"fields" => [
							new Assert\NotNull(),
							new Assert\Type("array"),
						],
						"allowExtraFields" => true,
						"allowMissingFields" => false,
					]),
				],
			]),
			$this->required ? new Assert\NotNull() : null,
			$this->required ? new Assert\Count(min: 1) : null,
		]);

		// validate nested fields
		foreach ($this->fields as $key => $field)
		{
			$field->validateData(
				$validator,
				$data[$key] ?? null,
			);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function transformValue (mixed $data, FieldValueTransformer $valueTransformer) : array
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
