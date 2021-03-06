<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data\IntegrationField;

final class IntegrationFieldEntry
{
	/**
	 */
	public function __construct (
		private readonly string $id,
		private readonly string $title,
		private readonly string $description,
		private readonly ?string $imageUrl,
		private readonly \DateTimeImmutable $lastUpdate,
		/**
		 * The normalized blob data
		 */
		private readonly array $blob = [],
	) {}

	/**
	 */
	public function getId () : string
	{
		return $this->id;
	}

	/**
	 */
	public function getTitle () : string
	{
		return $this->title;
	}

	/**
	 */
	public function getDescription () : string
	{
		return $this->description;
	}

	/**
	 */
	public function getImageUrl () : ?string
	{
		return $this->imageUrl;
	}

	/**
	 */
	public function getLastUpdate () : \DateTimeImmutable
	{
		return $this->lastUpdate;
	}

	/**
	 */
	public function getBlob () : array
	{
		return $this->blob;
	}
}
