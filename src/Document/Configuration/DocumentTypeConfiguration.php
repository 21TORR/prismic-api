<?php declare(strict_types=1);

namespace Torr\PrismicApi\Document\Configuration;

final class DocumentTypeConfiguration
{
	public function __construct (
		private string $label,
		private bool $isRepeatable = true,
		private bool $isActive = true,
	)
	{

	}

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