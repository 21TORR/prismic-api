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
		private string $url,
		private int $width,
		private int $height,
		private ?string $alt = null,
		private ?string $copyright = null,
	)
	{
	}

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
