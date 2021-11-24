<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Torr\Cli\Console\Style\TorrStyle;
use Torr\PrismicApi\Api\PrismicApi;
use Torr\PrismicApi\Document\Document;
use Torr\PrismicApi\Exception\Data\InvalidDocumentTypeException;

final class CustomTypesMigrator
{
	/**
	 */
	public function __construct (
		private ServiceLocator $documentTypes,
		private PrismicApi $api,
	)
	{
	}

	/**
	 * Check all document types before starting the migration
	 *
	 * @return string[]
	 */
	private function getDocumentTypes () : array
	{
		$result = [];

		foreach ($this->documentTypes->getProvidedServices() as $documentTypeFQCN)
		{
			if (!\is_a($documentTypeFQCN, Document::class, true))
			{
				throw new InvalidDocumentTypeException(\sprintf(
					"Document type '%s' must be a subclass of Document.",
					$documentTypeFQCN,
				));
			}

			$result[] = $documentTypeFQCN;
		}

		return $result;
	}

	/**
	 */
	public function migrateAllTypes (TorrStyle $style) : void
	{
		$style->section("Migrating all types");

		foreach ($this->getDocumentTypes() as $documentType)
		{
			\assert(\is_a($documentType, Document::class, true));

			$style->write(\sprintf(
				"• Migrating type <fg=blue>%s</> ... ",
				$documentType::configureType()->getLabel(),
			));
			$added = $this->api->pushTypeDefinition($documentType);


			$style->writeln(
				\sprintf("<fg=green>%s</>", $added ? "added" : "updated"),
			);
		}
	}
}
