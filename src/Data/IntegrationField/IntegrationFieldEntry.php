<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data\IntegrationField;

final class IntegrationFieldEntry
{
	/**
	 */
	public function __construct (
		private string $id,
		private string $title,
		private string $description,
		private ?string $imageUrl,
		private \DateTimeImmutable $lastUpdate,
		/**
		 * The normalized blob data
		 */
		private array $blob = [],
	)
	{
	}

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
