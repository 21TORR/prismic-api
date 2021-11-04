<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data;

interface PrismicTypeInterface
{
	/**
	 * Transforms the type to an array
	 */
	public function toArray () : array;
}
