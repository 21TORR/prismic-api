<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType;

use Torr\Cli\Console\Style\TorrStyle;
use Torr\PrismicApi\Api\PrismicApi;

final class CustomTypesMigrator
{
	public function __construct (
		private PrismicApi $api,
		private iterable $typeDefinitions,
	)
	{
	}

	/**
	 */
	public function migrateAllTypes (TorrStyle $style) : void
	{
		$style->section("Migrating all types");

		/** @var CustomTypeDefinition $definition */
		foreach ($this->typeDefinitions as $definition)
		{
			$style->write(\sprintf(
				"• Migrating type <fg=blue>%s</> ... ",
				$definition->getLabel(),
			));
			$added = $this->api->pushTypeDefinition($definition);


			$style->writeln(
				\sprintf("<fg=green>%s</>", $added ? "added" : "updated"),
			);
		}
	}
}
