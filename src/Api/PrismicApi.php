<?php declare(strict_types=1);

namespace Torr\PrismicApi\Api;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Torr\PrismicApi\CustomType\CustomTypeDefinition;
use Torr\PrismicApi\Data\Environment;
use Torr\PrismicApi\Exception\Api\RequestFailedException;

final class PrismicApi
{
	private string $repository;
	private string $contentToken;
	private string $typesToken;
	private HttpClientInterface $contentClient;
	private HttpClientInterface $typesClient;
	private LoggerInterface $logger;
	private ?Environment $environment = null;

	/**
	 */
	public function __construct (
		LoggerInterface $logger,
		string $repository,
		string $contentToken,
		string $typesToken,
	) {
		$this->repository = $repository;
		$this->contentToken = $contentToken;
		$this->typesToken = $typesToken;

		$this->contentClient = HttpClient::createForBaseUri("https://{$repository}.prismic.io/api/v2/");
		$this->typesClient = HttpClient::createForBaseUri("https://customtypes.prismic.io/");
		$this->logger = $logger;
	}


	/**
	 * Returns the prismic environment
	 */
	public function getEnvironment () : Environment
	{
		if (null === $this->environment)
		{
			$this->environment = new Environment($this->requestContent(""));
		}

		return $this->environment;
	}


	/**
	 * Searches for documents, according to the given predicates
	 */
	public function searchDocuments (
		string $predicates,
		?string $language = null,
		?string $ref = null,
	) : array
	{
		$allResults = [];
		$page = 1;
		$maxPage = 1;

		while ($page <= $maxPage)
		{
			$query = [
				"ref" => $ref ?? $this->getEnvironment()->getMasterRefId(),
				"q" => $predicates,
				"pageSize" => 100,
				"page" => $page,
			];

			if (null !== $language)
			{
				$query["lang"] = $language;
			}

			$response = $this->requestContent("documents/search", $query);

			foreach ($response["results"] as $result)
			{
				$allResults[] = $result;
			}

			$maxPage = $response["total_pages"];
			++$page;
		}

		return $allResults;
	}


	/**
	 * Pushes the type definition to prismic
	 *
	 * @return bool whether the type was newly created (true) or just updated (false)
	 */
	public function pushTypeDefinition (CustomTypeDefinition $typeDefinition) : bool
	{
		$stored = $this->fetchesTypeConfig($typeDefinition);
		$alreadyExists = [] !== $stored;

		$data = [];

		foreach ($typeDefinition->getTabs() as $tab)
		{
			$data[$tab->getLabel()] = $tab->toArray();
		}

		$this->requestType(
			path: $alreadyExists
				? "customtypes/update"
				: "customtypes/insert",
			payload: [
				"id" => $typeDefinition->getId(),
				"label" => $typeDefinition->getLabel(),
				"repeatable" => $typeDefinition->isRepeatable(),
				"json" => $data,
				"status" => $typeDefinition->isActive(),
			],
			method: "POST",
		);

		return !$alreadyExists;
	}


	// region Type Config specific fetcher
	/**
	 * Fetches the currently stored type config for the given type
	 */
	private function fetchesTypeConfig (CustomTypeDefinition $typeDefinition) : ?array
	{
		try
		{
			return $this->requestType("customtypes/{$typeDefinition->getId()}");
		}
		catch (RequestFailedException $exception)
		{
			$previous = $exception->getPrevious();

			if ($previous instanceof HttpExceptionInterface && 404 === $previous->getResponse()->getStatusCode())
			{
				return null;
			}

			throw $exception;
		}
	}
	// endregion


	// region HTTP wrappers
	/**
	 * Sends a basic request to the API
	 */
	private function requestContent (string $path, array $query = []) : array
	{
		return $this->performRequest(
			fn () => $this->contentClient->request(
				"GET",
				$path,
				(new HttpOptions())
					->setQuery(\array_replace($query, [
						"access_token" => $this->contentToken,
						"format" => "json",
					]))
					->toArray(),
			),
			[
				"path" => $path,
				"query" => $query,
			],
		);
	}

	/**
	 * Sends a custom type request to the API
	 */
	private function requestType (string $path, array $payload = [], string $method = "GET") : array
	{
		$options = (new HttpOptions())
			->setHeaders([
				"repository" => $this->repository,
			])
			->setAuthBearer($this->typesToken);

		if (!empty($payload))
		{
			$options->setJson($payload);
		}


		return $this->performRequest(
			fn () => $this->typesClient->request(
				$method,
				$path,
				$options->toArray(),
			),
			[
				"path" => $path,
				"payload" => $payload,
			],
		);
	}

	/**
	 * Low-level implementation to perform the request
	 */
	private function performRequest (callable $requestCallable, array $debugParameters = []) : array
	{
		try
		{
			$response = $requestCallable();

			return "" !== $response->getContent(false)
				? $response->toArray()
				: [];
		}
		catch (TransportExceptionInterface|HttpExceptionInterface $exception)
		{
			$this->logger->error("Prismic request failed: {message}", \array_replace($debugParameters, [
				"message" => $exception->getMessage(),
				"response" => $exception instanceof HttpExceptionInterface
					? $exception->getResponse()->getContent(false)
					: "n/a",
			]));

			throw new RequestFailedException("Prismic request failed", 0, $exception);
		}
		catch (DecodingExceptionInterface $exception)
		{
			$this->logger->error("Prismic request failed to decode: {message}", \array_replace($debugParameters, [
				"message" => $exception->getMessage(),
			]));

			throw new RequestFailedException("Prismic request decoding failed", 0, $exception);
		}
	}
	// endregion
}
