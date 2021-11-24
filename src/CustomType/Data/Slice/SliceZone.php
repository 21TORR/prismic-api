<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Slice;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\CustomType\Data\PrismicTypeInterface;
use Torr\PrismicApi\CustomType\Exception\InvalidTypeDefinitionException;
use Torr\PrismicApi\CustomType\Helper\KeyedMapHelper;
use Torr\PrismicApi\Exception\Data\InvalidDataException;
use Torr\PrismicApi\Exception\Transform\TransformationFailedException;
use Torr\PrismicApi\Transform\FieldValueTransformer;
use Torr\PrismicApi\Validation\SliceValidationCompound;

/**
 * @see https://prismic.io/docs/core-concepts/slices
 */
final class SliceZone implements PrismicTypeInterface
{
	/**
	 * @param array<string, Slice> $choices
	 */
	public function __construct (
		private string $label,
		private array $choices = [],
	)
	{
		if (empty($this->choices))
		{
			throw new InvalidTypeDefinitionException("Can't create a slice zone without slice choices");
		}
	}

	/**
	 */
	public function toArray () : array
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
	public function getValidationConstraints () : array
	{
		$sliceValidations = [];

		foreach ($this->choices as $key => $choice)
		{
			$sliceValidations[] = new SliceValidationCompound(
				$key,
				$choice->getValidationConstraints(),
			);
		}

		return [
			new Assert\NotNull(),
			new Assert\Type("array"),
			new Assert\All([
				"constraints" => [
					new Assert\AtLeastOneOf([
						"constraints" => $sliceValidations,
					]),
				],
			]),
		];
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
