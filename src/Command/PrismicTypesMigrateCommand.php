<?php declare(strict_types=1);

namespace Torr\PrismicApi\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Cli\Command\CommandHelper;
use Torr\Cli\Console\Style\TorrStyle;
use Torr\PrismicApi\CustomType\CustomTypesMigrator;
use Torr\PrismicApi\Exception\PrismicApiException;

#[AsCommand(
	name: "prismic:types:migrate",
	description: "Migrates all custom types",
)]
final class PrismicTypesMigrateCommand extends Command
{
	/**
	 * @inheritDoc
	 */
	public function __construct (
		private CustomTypesMigrator $typesMigrator,
		private CommandHelper $commandHelper,
	)
	{
		parent::__construct(null);
	}


	/**
	 * @inheritDoc
	 */
	protected function execute (InputInterface $input, OutputInterface $output) : int
	{
		$this->commandHelper->startLongRunningCommand();
		$io = new TorrStyle($input, $output);

		$io->title("Prismic: Migrate Custom Types");

		try
		{
			$this->typesMigrator->migrateAllTypes($io);
			$io->success("All migrations finished successfully");

			return 0;
		}
		catch (PrismicApiException $exception)
		{
			$io->error(\sprintf("Running the migrations failed: %s", $exception->getMessage()));

			return 1;
		}
	}


}
