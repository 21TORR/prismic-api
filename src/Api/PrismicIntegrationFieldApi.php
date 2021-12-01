<?php declare(strict_types=1);

namespace Torr\PrismicApi\Api;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Torr\PrismicApi\Data\IntegrationField\IntegrationFieldEntry;
use Torr\PrismicApi\Exception\IntegrationField\IntegrationFieldApiException;

final class PrismicIntegrationFieldApi
{
	public const ACTION_CREATE = "create";
	public const ACTION_UPDATE = "update";
	public const ACTION_DELETE = "delete";
	public const ACTION_RESET = "reset";

	private HttpClientInterface $client;

	/**
	 */
	public function __construct (
		private string $repository,
		/**
		 * @var array<string, string>
		 */
		private array $tokens,
		private LoggerInterface $logger,
	)
	{
		$this->client = HttpClient::createForBaseUri("https://if-api.prismic.io");
	}

	/**
	 * Resets the field data and imports the given entries
	 *
	 * @param IntegrationFieldEntry[] $entries
	 */
	public function atomicInsert (string $integrationFieldKey, array $entries) : bool
	{
		// first reset index
		$status = $this->sendRequest($integrationFieldKey, self::ACTION_RESET);

		if (200 !== $status)
		{
			return false;
		}

		// then index
		$status = $this->sendRequest($integrationFieldKey, self::ACTION_UPDATE, $entries);
		return 200 === $status;
	}


	/**
	 * @param IntegrationFieldEntry[]|null $entries
	 *
	 * @return int the HTTP status code of the call
	 */
	public function sendRequest (
		string $integrationFieldKey,
		string $action,
		?array $entries = null,
	) : int
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

			return $response->getStatusCode();
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
