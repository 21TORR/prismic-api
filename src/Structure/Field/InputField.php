<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraint;
use Torr\PrismicApi\Structure\PrismicTypeInterface;
use Torr\PrismicApi\Transform\DataTransformer;
use Torr\PrismicApi\Validation\DataValidator;
use Torr\PrismicApi\Visitor\DataVisitorInterface;

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
	 *
	 * @template T
	 *
	 * @param T $data
	 *
	 * @return T
	 */
	public function transformValue (
		mixed $data,
		DataTransformer $dataTransformer,
		?DataVisitorInterface $dataVisitor = null,
	) : mixed
	{
		$dataVisitor?->onDataVisit($this, $data);

		return $data;
	}


	/**
	 * Ensures that the value is valid
	 *
	 * @param array<Constraint|null> $constraints
	 */
	protected function ensureDataIsValid (
		DataValidator $validator,
		array $path,
		mixed $data,
		array $constraints,
	) : void
	{
		$validator->ensureDataIsValid(
			$path,
			static::class,
			$data,
			$constraints,
		);
	}
}
