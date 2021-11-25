<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Torr\PrismicApi\Structure\PrismicTypeInterface;
use Torr\PrismicApi\Transform\FieldValueTransformer;

/**
 * Low-level input field wrapper
 */
abstract class InputField implements PrismicTypeInterface
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
	public function formatTypeDefinition () : array
	{
		return [
			"type" => $this->type,
			"config" => $this->config,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function transformValue (mixed $data, FieldValueTransformer $valueTransformer) : mixed
	{
		return $data;
	}
}
