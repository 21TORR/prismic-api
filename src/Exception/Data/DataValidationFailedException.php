<?php declare(strict_types=1);

namespace Torr\PrismicApi\Exception\Data;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Torr\PrismicApi\Exception\PrismicApiException;

final class DataValidationFailedException extends \InvalidArgumentException implements PrismicApiException
{
	/**
	 */
	public function __construct (
		private readonly array $path,
		private readonly mixed $data,
		private readonly string $fieldType,
		?string $errorMessage = null,
		private readonly ?ConstraintViolationListInterface $violations = null,
		?\Throwable $previous = null,
	)
	{
		parent::__construct(
			\sprintf(
				"Failed to validate data at path '%s' with field type '%s'%s",
				\implode(" / ", $path),
				$fieldType,
				null !== $errorMessage
					? ": {$errorMessage}"
					: "",
			),
			0,
			$previous,
		);
	}

	/**
	 */
	public function getViolations () : ?ConstraintViolationListInterface
	{
		return $this->violations;
	}

	/**
	 */
	public function getData () : mixed
	{
		return $this->data;
	}

	/**
	 */
	public function getPath () : array
	{
		return $this->path;
	}

	/**
	 */
	public function getFieldType () : string
	{
		return $this->fieldType;
	}
}
