<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validation;
use Torr\PrismicApi\Exception\Data\InvalidDataStructureException;

trait DataStructureValidationTrait
{
	/**
	 * Validates the given data according to the constraints.
	 *
	 * @param string|null $validationMessage A message that is added to the exception in case the validation fails.
	 */
	private function validateDataStructure (array $data, ?Constraint $constraint, ?string $validationMessage = null) : void
	{
		// always valid if no constraints given
		if (null === $constraint)
		{
			return;
		}

		$validator = Validation::createValidator();
		$violations = $validator->validate($data, $constraint);

		if (\count($violations) > 0)
		{
			throw new InvalidDataStructureException(
				static::class,
				$violations,
				$validationMessage,
			);
		}
	}
}