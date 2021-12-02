<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Slice;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Exception\Data\InvalidDataException;
use Torr\PrismicApi\Exception\Structure\InvalidTypeDefinitionException;
use Torr\PrismicApi\Exception\Transform\TransformationFailedException;
use Torr\PrismicApi\Structure\Helper\KeyedMapHelper;
use Torr\PrismicApi\Structure\PrismicTypeInterface;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;
use Torr\PrismicApi\Transform\FieldValueTransformer;
use Torr\PrismicApi\Validation\SliceValidationCompound;

/**
 * @see https://prismic.io/docs/core-concepts/slices
 */
final class SliceZone implements PrismicTypeInterface
{
	use ValueValidationTrait;

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
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		// first check basic structure
		$this->ensureDataIsValid($validator, $data, [
			new Assert\NotNull(),
			new Assert\Type("array"),
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
