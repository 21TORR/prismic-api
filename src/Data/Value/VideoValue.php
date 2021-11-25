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
		private string $provider,
		private string $url,
		private string $title,
		private int $width,
		private int $height,
		private ImageValue $thumbnail,
	)
	{

	}

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
