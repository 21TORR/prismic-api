<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Part;

use Torr\PrismicApi\CustomType\Data\PrismicTypeInterface;
use Torr\PrismicApi\CustomType\Exception\InvalidTypeDefinitionException;

class ImageConstraint implements PrismicTypeInterface
{
	/**
	 */
	public function __construct (
		private ?int $width = null,
		private ?int $height = null,
	)
	{
		if (null === $this->width && null === $this->height)
		{
			throw new InvalidTypeDefinitionException("You must either define a width or a height (or both).");
		}
	}


	/**
	 * @inheritDoc
	 */
	public function toArray () : array
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
