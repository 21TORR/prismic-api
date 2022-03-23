<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data\Value;

final class DocumentLinkValue
{
	/**
	 */
	public function __construct (
		private string $id,
		private ?string $type,
	) {}

	/**
	 */
	public function getId () : string
	{
		return $this->id;
	}

	/**
	 */
	public function getType () : ?string
	{
		return $this->type;
	}
}
