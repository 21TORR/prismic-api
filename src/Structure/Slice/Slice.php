<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Slice;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Exception\Structure\InvalidTypeDefinitionException;
use Torr\PrismicApi\Exception\Transform\TransformationFailedException;
use Torr\PrismicApi\Structure\Field\InputField;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Helper\KeyedMapHelper;
use Torr\PrismicApi\Structure\PrismicTypeInterface;
use Torr\PrismicApi\Transform\DataTransformer;
use Torr\PrismicApi\Validation\DataValidator;
use Torr\PrismicApi\Visitor\DataVisitorInterface;

/**
 * You can extend this class to create reusable slices
 *
 * @see https://prismic.io/docs/core-concepts/slices
 */
abstract class Slice implements PrismicTypeInterface
{
	/**
	 * @param array<string, InputField> $fields
	 * @param array<string, InputField> $repeatedFields
	 */
	public function __construct (
		private readonly string $label,
		private readonly array $fields = [],
		private readonly array $repeatedFields = [],
		private readonly ?string $description = null,
		private readonly ?string $icon = null,
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
	public function validateData (DataValidator $validator, array $path, mixed $data) : void
	{
		// first validate the basic structure
		$validator->ensureDataIsValid(
			$path,
			static::class,
			$data,
			[
				new Assert\NotNull(),
				new Assert\Type("array"),
				new Assert\Collection([
					"fields" => [
						"primary" => [
							new Assert\NotNull(),
							new Assert\Type("array"),
						],
						"items" => [
							new Assert\NotNull(),
							new Assert\Type("array"),
						],
						"slice_label" => [
							new Assert\Type("string"),
						],
					],
					"allowExtraFields" => true,
					"allowMissingFields" => false,
				]),
			],
		);

		// validate (non-repeated) fields
		foreach ($this->fields as $key => $field)
		{
			$field->validateData(
				$validator,
				[...$path, "static", $key],
				$data["primary"][$key] ?? null,
			);
		}

		// validate repeated fields
		foreach ($data["items"] as $index => $itemsData)
		{
			foreach ($this->repeatedFields as $key => $field)
			{
				$field->validateData(
					$validator,
					[...$path, "repeated", $index, $key],
					$itemsData[$key] ?? null,
				);
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function transformValue (
		mixed $data,
		DataTransformer $dataTransformer,
		?DataVisitorInterface $dataVisitor = null,
	) : mixed
	{
		\assert(\is_array($data));
		$resultItems = [];
		$resultData = [];

		foreach ($this->fields as $key => $field)
		{
			$resultData[$key] = $this->transformSingleValue(
				$dataTransformer,
				$this->fields,
				$key,
				$data["primary"][$key] ?? null,
				$dataVisitor,
			);
		}

		foreach ($data["items"] as $itemsData)
		{
			$transformedItem = [];

			foreach ($this->repeatedFields as $key => $field)
			{
				$transformedItem[$key] = $this->transformSingleValue(
					$dataTransformer,
					$this->repeatedFields,
					$key,
					$itemsData[$key] ?? null,
					$dataVisitor,
				);
			}

			$resultItems[] = $transformedItem;
		}

		return [
			"data" => $resultData,
			"items" => $resultItems,
			"extra" => $dataTransformer->generateExtraDataForSlice($this),
		];
	}

	/**
	 * @param array<string, InputField> $fields
	 */
	private function transformSingleValue (
		DataTransformer $valueTransformer,
		array $fields,
		?string $key,
		mixed $fieldData,
		?DataVisitorInterface $dataVisitor,
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

		return $field->transformValue($fieldData, $valueTransformer, $dataVisitor);
	}
}
