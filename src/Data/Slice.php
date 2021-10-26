<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

abstract class Slice extends Dataset
{
	/**
	 * Base data of a slice: the type
	 */
	protected string $type;

	/**
	 * Base data of a slice: the label
	 */
	protected ?string $label;

	/**
	 * The base (non-repeated) fields of this slice
	 */
	protected array $base;

	/**
	 * The repeated items
	 */
	protected array $repeated;


	/**
	 */
	public function __construct (array $data)
	{
		parent::__construct($data);
		$this->type = $data["slice_type"];
		$this->label = $data["slice_label"];
		$this->base = $data["primary"];
		$this->repeated = $data["items"];
	}


	/**
	 * @inheritDoc
	 */
	protected function getValidationConstraints () : array
	{
		$fields = [
			"slice_type" => [
				new Assert\NotNull(),
				new Assert\Type("string"),
			],
			"slice_label" => [
				new Assert\Type("string"),
			],
			"primary" => \array_filter([
				new Assert\NotNull(),
				new Assert\Type("array"),
				$this->getBaseValidationConstraint(),
			]),
			"items" => [
				new Assert\NotNull(),
				new Assert\Type("array"),
			],
		];

		$repeatedConstraints = $this->getRepeatedValidationConstraint();

		if (null !== $repeatedConstraints)
		{
			$fields["items"][] = new Assert\All([
				"constraints" => [$repeatedConstraints],
			]);
		}

		return [
			new Assert\Collection([
				"fields" => $fields,
				"allowMissingFields" => false,
				"allowExtraFields" => true,
			]),
		];
	}


	/**
	 * Returns the constraint to validate the repeated items.
	 * Return null if you don't use repeated items or validate them somewhere else.
	 */
	abstract protected function getRepeatedValidationConstraint () : ?Constraint;


	/**
	 * Returns the constraint to validate the base data of this slice.
	 * Return null if you don't use the base data items.
	 */
	abstract protected function getBaseValidationConstraint () : ?Constraint;
}
