<?php declare(strict_types=1);

namespace Torr\PrismicApi\RichText\Link;

final class WebLinkHandler implements LinkGeneratorHandler
{
	public function handleLink (array $linkData) : ?string
	{
		return ("Web" === $linkData["link_type"])
			? $linkData["url"]
			: null;
	}
}
