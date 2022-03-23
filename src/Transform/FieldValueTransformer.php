<?php declare(strict_types=1);

namespace Torr\PrismicApi\Transform;

use Torr\PrismicApi\Structure\Field\InputField;
use Torr\PrismicApi\Structure\Slice\Slice;
use Torr\PrismicApi\Structure\Slice\SliceZone;
use Torr\PrismicApi\Transform\Slice\SliceExtraDataGeneratorInterface;

final class FieldValueTransformer
{
	public function __construct (
		/** @var iterable<SliceExtraDataGeneratorInterface> */
		private iterable $sliceExtraDataGenerators,
	) {}

	public function transformValue (
		array $data,
		InputField|SliceZone $field,
	) : mixed
	{
		return $field->transformValue($data, $this);
	}


	/**
	 * Transforms rich text.
	 * It mainly resolves internal links to their URL.
	 */
	public function transformRichText (array $data) : array
	{
		return $data;
	}

	/**
	 * Generates the extra data for the given slice
	 */
	public function generateExtraDataForSlice (Slice $slice) : array
	{
		$result = [];

		/** @var SliceExtraDataGeneratorInterface $dataGenerator */
		foreach ($this->sliceExtraDataGenerators as $dataGenerator)
		{
			$result = $dataGenerator->appendExtraData($slice, $result);
		}

		return $result;
	}
}
