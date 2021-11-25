<?php declare(strict_types=1);

namespace Tests\Torr\PrismicApi\Fixtures;

trait DocumentDataTrait
{
	/**
	 */
	private function getExampleDocumentData (array $data = []) : array
	{
		$now = new \DateTimeImmutable();

		return [
			"id" => "test",
			"uid" => null,
			"type" => "test",
			"tags" => [],
			"first_publication_date" => $now->format(\DateTimeInterface::RFC3339),
			"last_publication_date" => $now->format(\DateTimeInterface::RFC3339),
			"lang" => "de-de",
			"data" => $data,
		];
	}
}
