<?php declare(strict_types=1);

namespace Torr\PrismicApi\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Exception\Data\DataValidationFailedException;

final class DataValidator
{
	/**
	 */
	public function __construct (
		private readonly ValidatorInterface $validator,
	) {}

	/**
	 * Ensures that the given data is valid
	 *
	 * @param Constraint[] $constraints
	 * @throws DataValidationFailedException
	 */
	public function ensureDataIsValid (
		array $path,
		string $fieldType,
		mixed $data,
		array $constraints,
	) : void
	{
		// filter out null constraints
		$constraints = \array_filter(
			$constraints,
			static fn (?Constraint $constraint) => null !== $constraint,
		);

		$violations = $this->validator->validate($data, $constraints);

		if (\count($violations) > 0)
		{
			$errorMessage = $violations instanceof \Stringable
				? (string) $violations
				: null;

			throw new DataValidationFailedException(
				$path,
				$data,
				self::getBaseClassName($fieldType),
				$errorMessage,
				$violations,
			);
		}
	}

	/**
	 */
	public static function getBaseClassName (string $class, ?string $suffix = null) : string
	{
		$lastPortion = \strrchr($class, "\\");
		$baseClassName = false !== $lastPortion
			? \ltrim($lastPortion, "\\")
			: $class;

		if (null !== $suffix && \str_ends_with($baseClassName, $suffix))
		{
			$baseClassName = \substr($baseClassName, 0, -\strlen($suffix));
		}

		return $baseClassName;
	}
}
