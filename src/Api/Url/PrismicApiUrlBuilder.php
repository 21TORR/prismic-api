<?php declare(strict_types=1);

namespace Torr\PrismicApi\Api\Url;

/**
 * We need to build URLs that can have duplicate URL parameters, like
 *
 * test?q=1&q=2
 *
 * PHP itself doesn't allow that, they would produce
 *
 * test?q[]=1&q[]=2
 *
 * To work around that, we use a custom URL builder here.
 */
final class PrismicApiUrlBuilder
{
	/**
	 * Builds the API URL
	 */
	public function buildUrl (string $baseUrl, array $query) : string
	{
		$parts = [];

		foreach ($query as $key => $value)
		{
			if (\is_array($value))
			{
				foreach ($value as $nestedValue)
				{
					$parts[] = http_build_query([$key => $nestedValue], '', '&', \PHP_QUERY_RFC3986);
				}
			}
			else
			{
				$parts[] = http_build_query([$key => $value], '', '&', \PHP_QUERY_RFC3986);
			}
		}

		return !empty($parts)
			? "{$baseUrl}?" . \implode("&", $parts)
			: $baseUrl;
	}
}
