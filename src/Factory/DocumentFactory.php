<?php declare(strict_types=1);

namespace Torr\PrismicApi\Factory;

use Torr\PrismicApi\Data\Document;
use Torr\PrismicApi\Definition\DocumentDefinition;
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
	) {}


	/**
	 * @phpstan-template T of Document
	 * @phpstan-param DocumentDefinition<T> $definition
	 * @phpstan-return T
	 */
	public function createDocument (DocumentDefinition $definition, array $data) : Document
	{
		return $definition->createDocument($data, $this->validator);
	}


	/**
	 * @phpstan-template T of Document
	 * @phpstan-param class-string<T> $documentType
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
