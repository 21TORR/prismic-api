<?php declare(strict_types=1);

namespace Torr\PrismicApi;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Torr\BundleHelpers\Bundle\BundleExtension;
use Torr\PrismicApi\RichText\Link\LinkGeneratorHandler;

final class PrismicApiBundle extends Bundle
{
	/**
	 * @inheritDoc
	 */
	public function getContainerExtension () : ExtensionInterface
	{
		return new BundleExtension($this);
	}

	public function build (ContainerBuilder $container) : void
	{
		$container->registerForAutoconfiguration(LinkGeneratorHandler::class)
			->addTag("prismic.link_generator");
	}


	/**
	 * @inheritDoc
	 */
	public function getPath () : string
	{
		return \dirname(__DIR__);
	}
}
