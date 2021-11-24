<?php declare(strict_types=1);

namespace Torr\PrismicApi\Api;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Torr\PrismicApi\Data\Environment;
use Torr\PrismicApi\Document\Document;
use Torr\PrismicApi\Document\Factory\DocumentFactory;
use Torr\PrismicApi\Exception\Api\RequestFailedException;
use Torr\PrismicApi\Exception\Data\InvalidDocumentTypeException;

final class PrismicApi
{
	private HttpClientInterface $contentClient;
	private HttpClientInterface $typesClient;
	private ?Environment $environment = null;

	/**
	 */
	public function __construct (
		private LoggerInterface $logger,
		private DocumentFactory $documentFactory,
		private string $repository,
		private string $contentToken,
		private string $typesToken,
	) {
		$this->contentClient = HttpClient::createForBaseUri("https://{$repository}.prismic.io/api/v2/");
		$this->typesClient = HttpClient::createForBaseUri("https://customtypes.prismic.io/");
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
	 *
	 * @phpstan-template T of Document
	 * @phpstan-param class-string<T> $documentType
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
		if (!\is_a($documentType, Document::class, true))
		{
			throw new InvalidDocumentTypeException(\sprintf(
				"Document type '%s' must be a subclass of Document.",
				$documentType,
			));
		}

		$allResults = [];
		$page = 1;
		$maxPage = 1;

		$predicates[] = \sprintf(
			'[[at(document.type, "%s")]]',
			$documentType::getDocumentTypeId(),
		);

		while ($page <= $maxPage)
		{
			$query = [
				"ref" => $ref ?? $this->getEnvironment()->getMasterRefId(),
				"q" => \implode("", $predicates),
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
				$allResults[] = $this->documentFactory->createDocument($documentType, $result);
			}

			$maxPage = $response["total_pages"];
			++$page;
		}

		return $allResults;
	}


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
