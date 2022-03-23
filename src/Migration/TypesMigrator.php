<?php declare(strict_types=1);

namespace Torr\PrismicApi\Migration;

use Torr\Cli\Console\Style\TorrStyle;
use Torr\PrismicApi\Api\PrismicApi;
use Torr\PrismicApi\Definition\DocumentDefinition;

/**
 *
 */
final class TypesMigrator
{
	/**
	 * @param iterable<DocumentDefinition> $documentDefinitions
	 */
	public function __construct (
		private readonly iterable $documentDefinitions,
		private readonly PrismicApi $api,
	) {}


	/**
	 */
	public function migrateAllTypes (TorrStyle $style) : void
	{
		$style->section("Migrating all types");

		foreach ($this->documentDefinitions as $definition)
		{
			$style->write(\sprintf(
				"• Migrating type <fg=blue>%s</> ... ",
				$definition->configureType()->getLabel(),
			));
			$added = $this->api->pushTypeDefinition($definition);


			$style->writeln(
				\sprintf("<fg=green>%s</>", $added ? "added" : "updated"),
			);
		}
	}
}
