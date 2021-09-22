<?php declare(strict_types=1);

namespace Torr\PrismicApi\RichText\Link;

interface LinkGeneratorHandler
{
	public function handleLink (array $linkData) : ?string;
}
