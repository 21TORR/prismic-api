<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data;

/**
 * Low-level input field wrapper
 */
abstract class InputField
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
}
