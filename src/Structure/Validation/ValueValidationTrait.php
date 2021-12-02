<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Exception\Data\InvalidDataStructureException;

trait ValueValidationTrait
{
	/**
	 * Validates the data and possibly throws an exception
	 *
	 * @param Constraint[] $constraints
	 */
	private function ensureDataIsValid (
		ValidatorInterface $validator,
		mixed $data,
		array $constraints,
	) : void
	{
		// filter out null constraints
		$constraints = \array_filter(
			$constraints,
			static fn (?Constraint $constraint) => null !== $constraint,
		);

		$violations = $validator->validate($data, $constraints);

		if (\count($violations) > 0)
		{
			throw new InvalidDataStructureException(
				self::class,
				$data,
				$violations,
			);
		}
	}
}
