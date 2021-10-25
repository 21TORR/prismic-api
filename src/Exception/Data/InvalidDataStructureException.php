<?php declare(strict_types=1);

namespace Torr\PrismicApi\Exception\Data;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Torr\PrismicApi\Exception\PrismicApiException;

final class InvalidDataStructureException extends \InvalidArgumentException implements PrismicApiException
{
	private ?ConstraintViolationListInterface $violations;
	private array $data;
	private string $type;

	/**
	 */
	public function __construct (
		string $type,
		array $data,
		?ConstraintViolationListInterface $violations = null,
		?string $message = null,
		?\Throwable $previous = null,
	)
	{
		parent::__construct(
			\sprintf(
				"Failed to validate type '%s'%s%s",
				$type,
				null !== $message
					? " ({$message})"
					: "",
				$violations instanceof \Stringable
					? ": {$violations}"
					: "",
			),
			0,
			$previous,
		);

		$this->violations = $violations;
		$this->data = $data;
		$this->type = $type;
	}

	/**
	 */
	public function getViolations () : ?ConstraintViolationListInterface
	{
		return $this->violations;
	}

	/**
	 */
	public function getData () : array
	{
		return $this->data;
	}

	/**
	 */
	public function getType () : string
	{
		return $this->type;
	}
}
