<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data\Value;

final class VideoValue
{
	public const PROVIDER_YOUTUBE = "youtube";
	public const PROVIDER_VIMEO = "vimeo";

	public const PROVIDER_MAPPING = [
		"Vimeo" => self::PROVIDER_VIMEO,
		"YouTube" => self::PROVIDER_YOUTUBE,
	];

	/**
	 */
	public function __construct (
		private readonly string $provider,
		private readonly string $url,
		private readonly string $title,
		private readonly int $width,
		private readonly int $height,
		private readonly ImageValue $thumbnail,
	) {}

	/**
	 */
	public function getProvider () : string
	{
		return $this->provider;
	}

	/**
	 */
	public function getUrl () : string
	{
		return $this->url;
	}

	/**
	 */
	public function getTitle () : string
	{
		return $this->title;
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
	public function getThumbnail () : ImageValue
	{
		return $this->thumbnail;
	}
}
