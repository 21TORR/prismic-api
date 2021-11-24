<?php declare(strict_types=1);

namespace Torr\PrismicApi\Transform;

use Torr\PrismicApi\CustomType\Data\Field\InputField;
use Torr\PrismicApi\CustomType\Data\Slice\SliceZone;

final class FieldValueTransformer
{
	public function __construct ()
	{

	}

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
}
