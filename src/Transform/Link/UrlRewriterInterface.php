<?php declare(strict_types=1);

namespace Torr\PrismicApi\Transform\Link;

interface UrlRewriterInterface
{
	/**
	 * Rewrites the URL
	 */
	public function rewriteUrl (?string $url) : ?string;
}
