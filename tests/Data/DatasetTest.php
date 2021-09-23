<?php declare(strict_types=1);

namespace Tests\Torr\PrismicApi\Data;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Data\Dataset;
use Torr\PrismicApi\Exception\Data\InvalidDataStructureException;

final class DatasetTest extends TestCase
{
	/**
	 */
	public function provideConstruction () : iterable
	{
		yield "no constraint" => [["some" => "data"], null, true];

		yield "basic valid" => [
			["some" => "data"],
			new Assert\Collection([
				"fields" => [
					"some" => [
						new Assert\NotNull(),
						new Assert\Type("string"),
					],
				],
			]),
			true,
		];

		yield "basic invalid" => [
			["some" => null],
			new Assert\Collection([
				"fields" => [
					"some" => [
						new Assert\NotNull(),
						new Assert\Type("string"),
					],
				],
			]),
			false,
		];
	}


	/**
	 * @dataProvider provideConstruction
	 */
	public function testConstruction (array $data, ?Constraint $constraint, bool $shouldBeValid) : void
	{
		if (!$shouldBeValid)
		{
			$this->expectException(InvalidDataStructureException::class);
		}

		// construct and let possibly throw
		$this->createDataset($data, $constraint);

		if ($shouldBeValid)
		{
			$this->assertTrue(true);
		}
	}


	/**
	 */
	private function createDataset (array $data, ?Constraint $constraint) : Dataset
	{
		return new class ($data, $constraint) extends Dataset
		{
			public function __construct (array $data, private ?Constraint $constraint)
			{
				parent::__construct($data);
			}

			protected function getValidationConstraints () : ?Constraint
			{
				return $this->constraint;
			}
		};
	}
}
