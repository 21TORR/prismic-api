<?php declare(strict_types=1);

namespace Torr\PrismicApi\Api;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Torr\PrismicApi\Api\Url\PrismicApiUrlBuilder;
use Torr\PrismicApi\Data\Document;
use Torr\PrismicApi\Data\Environment;
use Torr\PrismicApi\Definition\DocumentDefinition;
use Torr\PrismicApi\Exception\Api\RequestFailedException;
use Torr\PrismicApi\Factory\DocumentFactory;

final class PrismicApi
{
	private HttpClientInterface $contentClient;
	private HttpClientInterface $typesClient;

	/**
	 */
	public function __construct (
		private readonly LoggerInterface $logger,
		private readonly DocumentFactory $documentFactory,
		private readonly PrismicApiUrlBuilder $urlBuilder,
		private readonly string $repository,
		private readonly string $contentToken,
		private readonly string $typesToken,
		HttpClientInterface $client,
	) {
		$this->contentClient = $client->withOptions(
			(new HttpOptions())
				->setBaseUri("https://{$repository}.prismic.io/api/v2/")
				->toArray(),
		);
		$this->typesClient = $client->withOptions(
			(new HttpOptions())
				->setBaseUri("https://customtypes.prismic.io/")
				->toArray(),
		);
	}


	/**
	 * Returns the prismic environment
	 */
	public function getEnvironment () : Environment
	{
		return new Environment($this->requestContent(""));
	}


	/**
	 * Searches for anything, according to the given predicates.
	 * Won't transform the results in any way.
	 *
	 * @return array[]
	 */
	public function search (
		array $predicates = [],
		?string $language = null,
		?string $ref = null,
	) : array
	{
		$allResults = [];
		$page = 1;
		$maxPage = 1;

		while ($page <= $maxPage)
		{
			$response = $this->requestContent("documents/search", [
				"ref" => $ref ?? $this->getEnvironment()->getMasterRefId(),
				"q" => $predicates,
				"pageSize" => 100,
				"page" => $page,
				"lang" => $language ?? "*",
			]);

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
	 * Searches for documents, according to the given predicates
	 *
	 * @phpstan-template T of Document
	 *
	 * @phpstan-param class-string<T> $documentType
	 *
	 * @phpstan-return T[]
	 *
	 * @return Document[]
	 */
	public function searchDocuments (
		string $documentType,
		array $predicates = [],
		?string $language = null,
		?string $ref = null,
	) : array
	{
		$definition = $this->documentFactory->getDefinitionForType($documentType);

		$predicates[] = \sprintf(
			'[[at(document.type, "%s")]]',
			$definition->getTypeId(),
		);

		$transformed = [];

		foreach ($this->search($predicates, $language, $ref) as $result)
		{
			$transformed[] = $this->documentFactory->createDocument($definition, $result);
		}

		return $transformed;
	}

	/**
	 * Pushes the type definition to prismic
	 *
	 * @return bool whether the type was newly created (true) or just updated (false)
	 */
	public function pushTypeDefinition (DocumentDefinition $definition) : bool
	{
		$stored = $this->fetchesTypeConfig($definition);
		$alreadyExists = [] !== $stored;

		$configuration = $definition->configureType();

		$this->requestType(
			path: $alreadyExists
				? "customtypes/update"
				: "customtypes/insert",
			payload: [
				"id" => $definition->getTypeId(),
				"label" => $configuration->getLabel(),
				"repeatable" => $configuration->isRepeatable(),
				"json" => $definition->getEditorTabs()->getTypesDefinition(),
				"status" => $configuration->isActive(),
			],
			method: "POST",
		);

		return !$alreadyExists;
	}


	// region Type Config specific fetcher
	/**
	 * Fetches the currently stored type config for the given type
	 */
	private function fetchesTypeConfig (DocumentDefinition $definition) : ?array
	{
		try
		{
			return $this->requestType("customtypes/{$definition->getTypeId()}");
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
		$pathWithQuery = $this->urlBuilder->buildUrl($path, \array_replace($query, [
			"access_token" => $this->contentToken,
			"format" => "json",
		]));

		return $this->performRequest(
			fn () => $this->contentClient->request(
				"GET",
				$pathWithQuery,
				(new HttpOptions())
					->setTimeout(60)
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
			->setTimeout(60)
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
			\assert($response instanceof ResponseInterface);

			return "" !== $response->getContent()
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
