<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Part;

class Thumbnail extends ImageConstraint
{
	public function __construct (
		private string $name,
		?int $width = null,
		?int $height = null,
	)
	{
		parent::__construct($width, $height);
	}

	/**
	 * @inheritDoc
	 */
	public function toArray () : array
	{
		return \array_replace(parent::toArray(), [
			"name" => $this->name,
		]);
	}
}
