<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data;

use Torr\PrismicApi\CustomType\Data\Field\InputField;
use Torr\PrismicApi\CustomType\Data\Slice\SliceZone;
use Torr\PrismicApi\CustomType\Helper\KeyedMapHelper;

/**
 * A single tab in the prismic editing UI / data structure
 */
final class TypeTab implements PrismicTypeInterface
{
	/**
	 * @param array<string, InputField|SliceZone> $fields
	 */
	public function __construct (
		private string $label,
		private array $fields,
	)
	{
	}

	/**
	 */
	public function getLabel () : string
	{
		return $this->label;
	}


	/**
	 *
	 */
	public function toArray () : array
	{
		return KeyedMapHelper::transformKeyedListOfTypes($this->fields);
	}
}
