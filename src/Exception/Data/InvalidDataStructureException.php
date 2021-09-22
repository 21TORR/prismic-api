<?php declare(strict_types=1);

namespace Torr\PrismicApi\Exception\Data;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Torr\PrismicApi\Exception\PrismicApiException;

final class InvalidDataStructureException extends \InvalidArgumentException implements PrismicApiException
{
	private ?ConstraintViolationListInterface $violations;

	/**
	 */
	public function __construct (
		string $type,
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
	}

	/**
	 */
	public function getViolations () : ?ConstraintViolationListInterface
	{
		return $this->violations;
	}
}
