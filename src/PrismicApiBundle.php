<?php declare(strict_types=1);

namespace Torr\PrismicApi;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Torr\BundleHelpers\Bundle\BundleExtension;
use Torr\PrismicApi\Definition\DocumentDefinition;
use Torr\PrismicApi\RichText\Link\LinkGeneratorHandler;
use Torr\PrismicApi\Transform\Link\UrlRewriterInterface;
use Torr\PrismicApi\Transform\Slice\SliceExtraDataGeneratorInterface;

final class PrismicApiBundle extends Bundle
{
	/**
	 * @inheritDoc
	 */
	public function getContainerExtension () : ExtensionInterface
	{
		return new BundleExtension($this);
	}

	/**
	 * @inheritDoc
	 */
	public function build (ContainerBuilder $container) : void
	{
		$container->registerForAutoconfiguration(LinkGeneratorHandler::class)
			->addTag("prismic.link_generator");

		$container->registerForAutoconfiguration(UrlRewriterInterface::class)
			->addTag("prismic.url_rewriter");

		$container->registerForAutoconfiguration(DocumentDefinition::class)
			->addTag("prismic.document.definition");

		$container->registerForAutoconfiguration(SliceExtraDataGeneratorInterface::class)
			->addTag("prismic.slice.extra-data-generator");
	}

	/**
	 * @inheritDoc
	 */
	public function getPath () : string
	{
		return \dirname(__DIR__);
	}
}
