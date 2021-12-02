<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Slice;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Exception\Structure\InvalidTypeDefinitionException;
use Torr\PrismicApi\Exception\Transform\TransformationFailedException;
use Torr\PrismicApi\Structure\Field\InputField;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Helper\KeyedMapHelper;
use Torr\PrismicApi\Structure\PrismicTypeInterface;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;
use Torr\PrismicApi\Transform\FieldValueTransformer;

/**
 * You can extend this class to create reusable slices
 *
 * @see https://prismic.io/docs/core-concepts/slices
 */
abstract class Slice implements PrismicTypeInterface
{
	use ValueValidationTrait;

	/**
	 * @param array<string, InputField> $fields
	 * @param array<string, InputField> $repeatedFields
	 */
	public function __construct (
		private string $label,
		private array $fields = [],
		private array $repeatedFields = [],
		private ?string $description = null,
		private ?string $icon = null,
	)
	{
		if (empty($this->fields) && empty($this->repeatedFields))
		{
			throw new InvalidTypeDefinitionException("Can't define slice without fields.");
		}
	}

	/**
	 */
	final public function formatTypeDefinition () : array
	{
		return FilterFieldsHelper::filterOptionalFields([
			"type" => "Slice",
			"fieldset" => $this->label,
			"description" => $this->description,
			"icon" => $this->icon,
			"non-repeat" => KeyedMapHelper::transformKeyedListOfTypes($this->fields),
			"repeat" => KeyedMapHelper::transformKeyedListOfTypes($this->repeatedFields),
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		$itemsConstraints = [];
		$primaryConstraints = [];

		if (!empty($this->fields))
		{
			$fields = [];

			foreach ($this->fields as $key => $field)
			{
				//$fields[$key] = $field->getValidationConstraints();
			}

			$primaryConstraints[] = new Assert\Collection([
				"fields" => $fields,
				"allowMissingFields" => true,
				"allowExtraFields" => true,
			]);
		}

		if (!empty($this->repeatedFields))
		{
			$fields = [];

			foreach ($this->repeatedFields as $key => $field)
			{
				//$fields[$key] = $field->getValidationConstraints();
			}

			$itemsConstraints[] = new Assert\All([
				"constraints" => [
					new Assert\Collection([
						"fields" => $fields,
						"allowMissingFields" => true,
						"allowExtraFields" => true,
					]),
				],
			]);
		}

		$this->ensureDataIsValid($validator, $data, [
			new Assert\NotNull(),
			new Assert\Type("array"),
			new Assert\Collection([
				"fields" => [
					"items" => [
						new Assert\NotNull(),
						new Assert\Type("array"),
						...$itemsConstraints,
					],
					"primary" => [
						new Assert\NotNull(),
						new Assert\Type("array"),
						...$primaryConstraints,
					],
				],
				"allowExtraFields" => true,
				"allowMissingFields" => false,
			]),
		]);
	}


	/**
	 * @inheritDoc
	 */
	public function transformValue (mixed $data, FieldValueTransformer $valueTransformer) : mixed
	{
		\assert(\is_array($data));
		$resultItems = [];
		$resultData = [];
		$result = [
			"data" => [],
			"items" => [],
			"extra" => [],
		];

		foreach ($this->fields as $key => $field)
		{
			$resultData[$key] = $this->transformSingleValue(
				$valueTransformer,
				$this->fields,
				$key,
				$data["primary"][$key] ?? null,
			);
		}

		foreach ($data["items"] as $itemsData)
		{
			$transformedItem = [];

			foreach ($this->repeatedFields as $key => $field)
			{
				$transformedItem[$key] = $this->transformSingleValue(
					$valueTransformer,
					$this->repeatedFields,
					$key,
					$itemsData[$key] ?? null,
				);
			}

			$resultItems[] = $transformedItem;
		}

		return [
			"data" => $resultData,
			"items" => $resultItems,
			"extra" => $valueTransformer->generateExtraDataForSlice($this),
		];
	}

	/**
	 * @param array<string, InputField> $fields
	 */
	private function transformSingleValue (
		FieldValueTransformer $valueTransformer,
		array $fields,
		?string $key,
		mixed $fieldData,
	) : mixed
	{
		if (null === $fieldData)
		{
			return null;
		}

		$field = $fields[$key] ?? null;

		if (null === $field)
		{
			throw new TransformationFailedException(\sprintf(
				"No field found for key '%s' in slice '%s'",
				$key,
				static::class,
			));
		}

		return $field->transformValue($fieldData, $valueTransformer);
	}
}
