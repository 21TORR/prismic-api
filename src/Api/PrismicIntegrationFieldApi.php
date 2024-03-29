<?php declare(strict_types=1);

namespace Torr\PrismicApi\Api;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Torr\Cli\Console\Style\TorrStyle;
use Torr\PrismicApi\Data\IntegrationField\IntegrationFieldEntry;
use Torr\PrismicApi\Exception\IntegrationField\IntegrationFieldApiException;

final class PrismicIntegrationFieldApi
{
	private const BATCH_SIZE = 200;
	public const ACTION_CREATE = "create";
	public const ACTION_UPDATE = "update";
	public const ACTION_DELETE = "delete";
	public const ACTION_RESET = "reset";

	private HttpClientInterface $client;

	/**
	 */
	public function __construct (
		private readonly string $repository,
		/** @var array<string, string> */
		private readonly array $tokens,
		private readonly LoggerInterface $logger,
		HttpClientInterface $client,
	)
	{
		$this->client = $client->withOptions(
			(new HttpOptions())
				->setBaseUri("https://if-api.prismic.io")
				->toArray(),
		);
	}

	/**
	 * Resets the field data and imports the given entries
	 *
	 * @param IntegrationFieldEntry[] $entries
	 */
	public function atomicInsert (
		string $integrationFieldKey,
		array $entries,
		?TorrStyle $io = null,
	) : bool
	{
		// first reset index
		$io?->writeln("• Clear existing entries");
		[$status, $response] = $this->sendRequest($integrationFieldKey, self::ACTION_RESET);

		if (200 !== $status)
		{
			$this->logger->error("Failed to reset the integration field '{field}', got status code {status}", [
				"field" => $integrationFieldKey,
				"status" => $status,
				"response" => $response,
			]);

			$io?->writeln("• <fg=red>Failed to reset</>");
			return false;
		}

		$io?->writeln("• <fg=green>Cleared all entries</>");

		// then index
		$batches = \ceil(\count($entries) / self::BATCH_SIZE);
		$io?->writeln(\sprintf(
			"• Importing all <fg=yellow>%d</> entries in <fg=yellow>%d</> batch%s",
			\count($entries),
			$batches,
			$batches > 1 ? "es" : "",
		));

		for ($i = 0; $i < $batches; ++$i)
		{
			$offset = $i * self::BATCH_SIZE;
			$io?->writeln(\sprintf(
				"• Importing batch <fg=blue>%d</> (entries <fg=yellow>%d</> – <fg=yellow>%d</>)",
				$i + 1,
				$offset,
				\min(\count($entries), ($i + 1) * self::BATCH_SIZE),
			));
			$currentBatch = \array_slice($entries, $offset, self::BATCH_SIZE);
			[$status, $response] = $this->sendRequest($integrationFieldKey, self::ACTION_UPDATE, $currentBatch);

			if (200 !== $status)
			{
				$this->logger->error("Failed to import the integration field '{field}' entries, got status code {status}", [
					"field" => $integrationFieldKey,
					"status" => $status,
					"response" => $response,
				]);

				$io?->writeln("• <fg=red>Failed to import</>");
				return false;
			}
		}

		$io?->writeln("• <fg=green>Imported all entries</>");
		return true;
	}


	/**
	 * @param IntegrationFieldEntry[]|null $entries
	 *
	 * @return array{int, string} the HTTP status code of the call + the response content
	 */
	public function sendRequest (
		string $integrationFieldKey,
		string $action,
		?array $entries = null,
	) : array
	{
		try {
			$options = (new HttpOptions())
				->setAuthBearer($this->getToken($integrationFieldKey));

			if (null !== $entries)
			{
				$options->setJson($this->normalizeEntries($entries));
			}

			$response = $this->client->request(
				"POST",
				$this->getUri($integrationFieldKey, $action),
				$options->toArray(),
			);

			return [$response->getStatusCode(), $response->getContent(false)];
		}
		catch (TransportExceptionInterface $e)
		{
			$this->logger->error("Failed to execute Integration Field data", [
				"exception" => $e,
				"field" => $integrationFieldKey,
			]);
			throw new IntegrationFieldApiException("Failed to execute Integration Field data: {$e->getMessage()}");
		}
	}

	/**
	 * @param IntegrationFieldEntry[] $entries
	 */
	private function normalizeEntries (array $entries) : array
	{
		$result = [];

		foreach ($entries as $entry)
		{
			$result[] = [
				"id" => $entry->getId(),
				"title" => $entry->getTitle(),
				"description" => $entry->getDescription(),
				"imageUrl" => $entry->getImageUrl() ?? "",
				"last_update" => $entry->getLastUpdate()->getTimestamp(),
				"blob" => (object) $entry->getBlob(),
			];
		}

		return $result;
	}


	private function getToken (string $integrationFieldKey) : string
	{
		$token = $this->tokens[$integrationFieldKey] ?? null;

		if (null === $token)
		{
			throw new IntegrationFieldApiException(\sprintf(
				"No integration field API token found for field with key '%s'",
				$integrationFieldKey,
			));
		}

		return $token;
	}


	/**
	 */
	private function getUri (string $integrationFieldKey, string $action) : string
	{
		$baseUri = "/if/write/{$this->repository}--{$integrationFieldKey}";

		return match($action)
		{
			self::ACTION_CREATE, self::ACTION_UPDATE => $baseUri,
			self::ACTION_DELETE => $baseUri . "/deleteItems",
			self::ACTION_RESET => $baseUri . "/reset",
			default => throw new IntegrationFieldApiException("Invalid action \"{$action}\""),
		};
	}
}
