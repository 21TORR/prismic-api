<?php declare(strict_types=1);

namespace Torr\PrismicApi\Document\Factory;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Document\Document;
use Torr\PrismicApi\Exception\Data\InvalidDocumentTypeException;

final class DocumentFactory
{
	public function __construct (
		private ValidatorInterface $validator,
	)
	{
	}

	/**
	 * @phpstan-template T of Document
	 * @phpstan-param class-string<T> $documentType
	 * @phpstan-return T
	 */
	public function createDocument (string $documentType, array $data) : Document
	{
		if (!\is_a($documentType, Document::class, true))
		{
			throw new InvalidDocumentTypeException(\sprintf(
				"Document type '%s' must be a subclass of Document.",
				$documentType,
			));
		}

		return $documentType::create($data, $this->validator);
	}
}
