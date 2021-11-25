<?php declare(strict_types=1);

namespace Torr\PrismicApi\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

final class SliceValidationCompound extends Compound
{
	/**
	 * @param Constraint[] $sliceConstraints
	 */
	public function __construct (
		string $key,
		private array $sliceConstraints,
	)
	{
		// we need to add the nested constraint before calling the constructor
		$this->sliceConstraints[] = new Assert\Collection([
			"fields" => [
				"slice_type" => [
					new Assert\NotNull(),
					new Assert\Type("string"),
					new Assert\IdenticalTo($key),
				],
			],
			"allowExtraFields" => true,
			"allowMissingFields" => false,
		]);

		parent::__construct();
	}


	/**
	 * @inheritDoc
	 */
	protected function getConstraints (array $options) : array
	{
		return $this->sliceConstraints;
	}
}
