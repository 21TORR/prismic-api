<?php declare(strict_types=1);

namespace Tests\Torr\PrismicApi\Data\Document;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Data\Document\Document;
use Torr\PrismicApi\Exception\Data\InvalidDataStructureException;

final class DocumentTest extends TestCase
{
	/**
	 */
	public function provideData () : iterable
	{
		$baseData = [
			"id" => "123",
			"uid" => null,
			"type" => "test",
			"tags" => [],
			"first_publication_date" => "2021-01-01T01:01:01+00:00",
			"last_publication_date" => "2021-01-01T01:01:01+00:00",
			"lang" => "de",
			"data" => [],
		];

		yield "minimal valid base date" => [
			$baseData,
			[],
			true,
		];

		yield "maximum valid base date" => [
			\array_replace($baseData, [
				"uid" => "test",
			]),
			[],
			true,
		];


		yield "no data at all" => [
			[],
			[],
			false,
		];

		// check for missing key
		foreach ($baseData as $key => $value)
		{
			$newData = $baseData;
			unset($newData[$key]);

			yield "missing key: {$key}" => [
				$newData,
				[],
				false,
			];
		}


		yield "custom data invalid" => [
			$baseData,
			[
				new Assert\Collection([
					"fields" => [
						"test" => [
							new Assert\NotNull(),
						],
					],
				]),
			],
			false,
		];

		yield "custom data valid" => [
			\array_replace($baseData, [
				"data" => [
					"test" => 1,
				],
			]),
			[
				new Assert\Collection([
					"fields" => [
						"test" => [
							new Assert\NotNull(),
						],
					],
				]),
			],
			true,
		];

		yield "data invalid type" => [
			\array_replace($baseData, [
				"data" => 1,
			]),
			[],
			false,
		];
	}


	/**
	 * @dataProvider provideData
	 *
	 * @param Constraint[] $constraints
	 */
	public function testConstruction (array $data, array $constraints, bool $shouldBeValid) : void
	{
		if (!$shouldBeValid)
		{
			$this->expectException(InvalidDataStructureException::class);
		}

		// construct and let possibly throw
		$this->createDocument($data, $constraints);

		if ($shouldBeValid)
		{
			$this->assertTrue(true);
		}
	}


	/**
	 * @param Constraint[] $constraints
	 */
	private function createDocument (array $data, array $constraints) : Document
	{
		return new class ($data, $constraints) extends Document
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
