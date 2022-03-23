<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Slice;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Exception\Data\DataValidationFailedException;
use Torr\PrismicApi\Exception\Data\InvalidDataException;
use Torr\PrismicApi\Exception\Structure\InvalidTypeDefinitionException;
use Torr\PrismicApi\Exception\Transform\TransformationFailedException;
use Torr\PrismicApi\Structure\Helper\KeyedMapHelper;
use Torr\PrismicApi\Structure\PrismicTypeInterface;
use Torr\PrismicApi\Transform\FieldValueTransformer;
use Torr\PrismicApi\Validation\DataValidator;

/**
 * @see https://prismic.io/docs/core-concepts/slices
 */
final class SliceZone implements PrismicTypeInterface
{
	public function __construct (
		private readonly string $label,
		/** @var array<string, Slice> */
		private readonly array $choices = [],
	)
	{
		if (empty($this->choices))
		{
			throw new InvalidTypeDefinitionException("Can't create a slice zone without slice choices");
		}
	}

	/**
	 */
	public function formatTypeDefinition () : array
	{
		return [
			"type" => "Slices",
			"fieldset" => $this->label,
			"config" => [
				"labels" => null,
				"choices" => KeyedMapHelper::transformKeyedListOfTypes($this->choices),
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function validateData (DataValidator $validator, array $path, mixed $data) : void
	{
		$validator->ensureDataIsValid(
			$path,
			static::class,
			$data,
			[
				new Assert\NotNull(),
				new Assert\Type("array"),
				// must be an array of arrays
				new Assert\All([
					new Assert\NotNull(),
					new Assert\Type("array"),
				])
			],
		);

		foreach ($data as $index => $nestedData)
		{
			\assert(\is_array($nestedData));

			// ensure that the slice type is available
			$validator->ensureDataIsValid(
				[...$path, $index],
				static::class,
				$nestedData,
				[
					new Assert\NotNull(),
					new Assert\Type("array"),
					// must be an array of arrays
					new Assert\Collection([
						"fields" => [
							"slice_type" => [
								new Assert\NotNull(),
								new Assert\Type("string"),
							]
						],
						"allowExtraFields" => true,
						"allowMissingFields" => false,
					])
				],
			);

			$sliceType = $nestedData["slice_type"];
			$slice = $this->choices[$sliceType] ?? null;

			if (!$slice instanceof Slice)
			{
				throw new DataValidationFailedException(
					[...$path, $index],
					$data,
					"Slice<?>",
					\sprintf("Unknown slice type '%s'", $sliceType),
				);
			}

			$slice->validateData(
				$validator,
				[
					...$path,
					\sprintf(
						"@%d Slice<%s>",
						$index,
						DataValidator::getBaseClassName(\get_class($slice), "Slice"),
					),
				],
				$nestedData,
			);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function validateD2ata (ValidatorInterface $validator, mixed $data) : void
	{
		// first check basic structure
		$this->ensureDataIsValid($validator, $data, [
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
					"slice_type" => [
						new Assert\NotNull(),
						new Assert\Type("string"),
					],
					"slice_label" => [
						new Assert\Type("string"),
					],
				],
				"allowExtraFields" => false,
				"allowMissingFields" => false,
			]),
		]);

		// then check every entry
	}


	/**
	 * @inheritDoc
	 */
	public function transformValue (mixed $data, FieldValueTransformer $valueTransformer) : mixed
	{
		$result = [];

		foreach ($data as $entryData)
		{
			$sliceType = $entryData["slice_type"] ?? null;
			$slice = $this->choices[$sliceType] ?? null;

			if (null === $slice)
			{
				throw new InvalidDataException(\sprintf(
					"Could not find configured slice for key '%s'",
					$sliceType,
				));
			}

			$transformed = $slice->transformValue($entryData, $valueTransformer);

			if (!\is_array($transformed) || \array_key_exists("type", $transformed))
			{
				throw new TransformationFailedException(\sprintf(
					"Failed to transform value of slice type '%s', the transformed value must be an array and it may not have a 'type' key.",
					\get_debug_type($slice),
				));
			}

			$transformed["type"] = $sliceType;
			$result[] = $transformed;
		}

		return $result;
	}
}
