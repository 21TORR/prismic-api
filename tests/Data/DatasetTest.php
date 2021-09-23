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
		yield "no constraint" => [["some" => "data"], [], true];

		yield "basic valid" => [
			["some" => "data"],
			[
				new Assert\Collection([
					"fields" => [
						"some" => [
							new Assert\NotNull(),
							new Assert\Type("string"),
						],
					],
				]),
			],
			true,
		];

		yield "basic invalid" => [
			["some" => null],
			[
				new Assert\Collection([
					"fields" => [
						"some" => [
							new Assert\NotNull(),
							new Assert\Type("string"),
						],
					],
				]),
			],
			false,
		];

		yield "multiple constraints" => [
			[
				"some" => "data",
				"other" => "data",
			],
			[
				new Assert\Collection([
					"fields" => [
						"some" => [
							new Assert\NotNull(),
							new Assert\Type("string"),
						],
					],
					"allowExtraFields" => true,
				]),
				new Assert\Collection([
					"fields" => [
						"other" => [
							new Assert\NotNull(),
							new Assert\Type("string"),
						],
					],
					"allowExtraFields" => true,
				]),
			],
			true,
		];
	}


	/**
	 * @dataProvider provideConstruction
	 * @param Constraint[] $constraints
	 */
	public function testConstruction (array $data, array $constraints, bool $shouldBeValid) : void
	{
		if (!$shouldBeValid)
		{
			$this->expectException(InvalidDataStructureException::class);
		}

		// construct and let possibly throw
		$this->createDataset($data, $constraints);

		if ($shouldBeValid)
		{
			$this->assertTrue(true);
		}
	}


	/**
	 * @param Constraint[] $constraints
	 */
	private function createDataset (array $data, array $constraints) : Dataset
	{
		return new class ($data, $constraints) extends Dataset
		{
			public function __construct (array $data, private array $constraints)
			{
				parent::__construct($data);
			}

			protected function getValidationConstraints () : array
			{
				return $this->constraints;
			}
		};
	}
}
