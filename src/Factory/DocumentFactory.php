<?php declare(strict_types=1);

namespace Torr\PrismicApi\Factory;

use Psr\Log\LoggerInterface;
use Torr\PrismicApi\Data\Document;
use Torr\PrismicApi\Definition\DocumentDefinition;
use Torr\PrismicApi\Exception\Data\DataValidationFailedException;
use Torr\PrismicApi\Exception\Document\MissingDocumentDefinitionException;
use Torr\PrismicApi\Validation\DataValidator;

final class DocumentFactory
{
	private ?array $definitionMap = null;

	/**
	 * @param iterable<DocumentDefinition> $documentDefinitions
	 */
	public function __construct (
		private readonly iterable $documentDefinitions,
		private readonly DataValidator $validator,
		private readonly LoggerInterface $logger,
	) {}


	/**
	 * @phpstan-template T of Document
	 *
	 * @phpstan-param DocumentDefinition<T> $definition
	 *
	 * @phpstan-return T
	 */
	public function createDocument (DocumentDefinition $definition, array $data) : Document
	{
		try
		{
			return $definition->createDocument($data, $this->validator);
		}
		catch (DataValidationFailedException $exception)
		{
			// catch exception to add proper logging
			$this->logger->error("Could not created document of type {type}, due to validation errors", [
				"exception" => $exception,
				"data" => $data,
				"type" => $definition->getTypeId(),
			]);

			throw $exception;
		}
	}


	/**
	 * @phpstan-template T of Document
	 *
	 * @phpstan-param class-string<T> $documentType
	 *
	 * @phpstan-return DocumentDefinition<T>
	 */
	public function getDefinitionForType (string $documentType) : DocumentDefinition
	{
		$definition = $this->getDocumentDefinitionMap()[$documentType] ?? null;

		if (null === $definition)
		{
			throw new MissingDocumentDefinitionException(\sprintf(
				"Can't find document definition for document type '%s'",
				$documentType,
			));
		}

		return $definition;
	}


	/**
	 * @return array<string, DocumentDefinition>
	 */
	private function getDocumentDefinitionMap () : array
	{
		if (null === $this->definitionMap)
		{
			$this->definitionMap = [];

			foreach ($this->documentDefinitions as $definition)
			{
				$this->definitionMap[$definition->getDataClass()] = $definition;
			}
		}

		return $this->definitionMap;
	}
}
