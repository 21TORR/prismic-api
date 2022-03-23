<?php declare(strict_types=1);

namespace Torr\PrismicApi\Definition\Configuration;

final class DocumentTypeConfiguration
{
	public function __construct (
		private readonly string $label,
		private readonly bool $isRepeatable = true,
		private readonly bool $isActive = true,
	) {}

	/**
	 */
	public function getLabel () : string
	{
		return $this->label;
	}

	/**
	 */
	public function isRepeatable () : bool
	{
		return $this->isRepeatable;
	}

	/**
	 */
	public function isActive () : bool
	{
		return $this->isActive;
	}
}
