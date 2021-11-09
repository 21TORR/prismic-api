<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Slice;

use Torr\PrismicApi\CustomType\Data\PrismicTypeInterface;
use Torr\PrismicApi\CustomType\Helper\KeyedMapHelper;

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
}
