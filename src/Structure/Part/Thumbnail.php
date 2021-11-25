<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Part;

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
	public function formatTypeDefinition () : array
	{
		return \array_replace(parent::formatTypeDefinition(), [
			"name" => $this->name,
		]);
	}
}
