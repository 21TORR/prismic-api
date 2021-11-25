<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Helper;

use Torr\PrismicApi\Structure\PrismicTypeInterface;

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
			$result[$key] = $entry->formatTypeDefinition();
		}

		return $result;
	}
}
