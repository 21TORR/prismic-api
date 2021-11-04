<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

use Torr\PrismicApi\CustomType\Data\PrismicTypeInterface;

/**
 * Low-level input field wrapper
 */
class InputField implements PrismicTypeInterface
{
	private string $type;
	private array $config;

	/**
	 */
	public function __construct (string $type, array $config)
	{
		$this->type = $type;
		$this->config = $config;
	}

	/**
	 */
	public function toArray () : array
	{
		return [
			"type" => $this->type,
			"config" => $this->config,
		];
	}

	/**
	 */
	protected function filterOptionalFields (array $config) : array
	{
		return \array_filter(
			$config,
			static fn ($entry) => null !== $entry,
		);
	}
}
