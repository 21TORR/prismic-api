<?php
declare(strict_types=1);

namespace Torr\PrismicApi\Api;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Torr\PrismicApi\Data\PrismicEnvironment;
use Torr\PrismicApi\DataFactory\PrismicEnvironmentFactory;
use Torr\PrismicApi\Exception\Api\RequestFailedException;

final class PrismicClient
{
	private HttpClientInterface $httpClient;
	private ?PrismicEnvironment $environment = null;

	/**
	 */
	public function __construct (
		string $baseUrl,
		private string $permanentAccessToken,
		private PrismicEnvironmentFactory $configFactory,
		private LoggerInterface $logger,
	)
	{
		$this->httpClient = HttpClient::createForBaseUri(
		// ensure trailing slash
			\rtrim($baseUrl, "/") . "/",
		);
	}


	/**
	 * Searches for documents, according to the given predicates
	 */
	public function searchDocuments (string $predicates) : array
	{
		$environment = $this->getEnvironment();
		$allResults = [];
		$page = 1;
		$maxPage = 1;

		while ($page <= $maxPage)
		{
			$response = $this->request("documents/search", [
				"ref" => $environment->getMasterRefId(),
				"q" => $predicates,
				"pageSize" => 100,
				"page" => $page,
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
	 * Gets or fetches the environment
	 */
	private function getEnvironment () : PrismicEnvironment
	{
		if (null === $this->environment)
		{
			// the base path will return the environment data
			$this->environment = $this->configFactory->createEnvironment($this->request("", []));
		}

		return $this->environment;
	}

	//region Search Request Wrappers
	/**
	 * Sends a basic request to the API
	 */
	private function request (string $path, array $query) : array
	{
		try
		{
			$response = $this->httpClient->request(
				"GET",
				$path,
				(new HttpOptions())
					->setQuery(\array_replace($query, [
						"access_token" => $this->permanentAccessToken,
						"format" => "json",
					]))
					->toArray(),
			);

			return $response->toArray();
		}
		catch (TransportExceptionInterface|HttpExceptionInterface $exception)
		{
			$this->logger->error("Prismic request failed: {message}", [
				"message" => $exception->getMessage(),
				"path" => $path,
				"query" => $query,
			]);

			throw new RequestFailedException("Prismic request failed", 0, $exception);
		}
		catch (DecodingExceptionInterface $exception)
		{
			$this->logger->error("Prismic request failed to decode: {message}", [
				"message" => $exception->getMessage(),
				"path" => $path,
				"query" => $query,
			]);

			throw new RequestFailedException("Prismic request decoding failed", 0, $exception);
		}
	}
	//endregion
}
