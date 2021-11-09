<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Helper;

/**
 * Helper class for filtering the fields
 */
final class FilterFieldsHelper
{
	/**
	 */
	public static function filterOptionalFields (array $config) : array
	{
		return \array_filter(
			$config,
			static fn ($entry) => null !== $entry && [] !== $entry,
		);
	}
}
