<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data\Value;

/**
 *
 */
final class ImageValue
{
	/**
	 */
	public function __construct (
		private readonly string $url,
		private readonly int $width,
		private readonly int $height,
		private readonly ?string $alt = null,
		private readonly ?string $copyright = null,
	) {}

	/**
	 */
	public function getUrl () : string
	{
		return $this->url;
	}

	/**
	 */
	public function getWidth () : int
	{
		return $this->width;
	}

	/**
	 */
	public function getHeight () : int
	{
		return $this->height;
	}

	/**
	 */
	public function getAlt () : ?string
	{
		return $this->alt;
	}

	/**
	 */
	public function getCopyright () : ?string
	{
		return $this->copyright;
	}
}
