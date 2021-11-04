<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Helper;

use Torr\PrismicApi\CustomType\Data\PrismicTypeInterface;

/**
 * Helper class for transforming the types
 */
final class KeyedMapHelper
{
	/**
	 * @param array<string, PrismicTypeInterface> $types
	 */
	public static function transformKeyedListOfTypes (array $types) : array
	{
		$result = [];

		foreach ($types as $key => $entry)
		{
			$result[$key] = $entry->toArray();
		}

		return $result;
	}
}
