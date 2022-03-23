<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Part;

use Torr\PrismicApi\Exception\Structure\InvalidTypeDefinitionException;

class ImageConstraint
{
	/**
	 */
	public function __construct (
		private readonly ?int $width = null,
		private readonly ?int $height = null,
	)
	{
		if (null === $this->width && null === $this->height)
		{
			throw new InvalidTypeDefinitionException("You must either define a width or a height (or both).");
		}
	}


	/**
	 *
	 */
	public function formatTypeDefinition () : array
	{
		return \array_filter(
			[
				"width" => $this->width,
				"height" => $this->height,
			],
			static fn (?int $entry) => null !== $entry,
		);
	}
}
