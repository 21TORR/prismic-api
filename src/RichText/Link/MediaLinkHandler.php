<?php
declare(strict_types=1);

namespace Torr\PrismicApi\RichText\Link;

final class MediaLinkHandler implements LinkGeneratorHandler
{
	public function handleLink (array $linkData) : ?string
	{
		return ("Media" === $linkData["link_type"])
			? $linkData["url"]
			: null;
	}
}
