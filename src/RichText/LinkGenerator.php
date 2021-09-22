<?php declare(strict_types=1);

namespace Torr\PrismicApi\RichText;

use Torr\PrismicApi\RichText\Link\LinkGeneratorHandler;

final class LinkGenerator
{
	/** @var iterable<LinkGeneratorHandler> */
	private iterable $handlers;

	/**
	 * @param iterable<LinkGeneratorHandler> $handlers
	 */
	public function __construct (iterable $handlers)
	{
		$this->handlers = $handlers;
	}


	public function getUrl (array $link) : ?string
	{
		foreach ($this->handlers as $handler)
		{
			$url = $handler->handleLink($link);

			if (null !== $url)
			{
				return $url;
			}
		}

		return null;
	}
}
