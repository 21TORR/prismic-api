<?php declare(strict_types=1);

namespace Torr\PrismicApi\Twig;

use Torr\PrismicApi\Image\ImageBuilder;
use Torr\PrismicApi\RichText\LinkGenerator;
use Torr\PrismicApi\RichText\RichTextRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PrismicTwigExtension extends AbstractExtension
{
	public function __construct (
		private RichTextRenderer $richTextRenderer,
		private ImageBuilder $imageBuilder,
		private LinkGenerator $linkRenderer,
	)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getFunctions () : array
	{
		return [
			new TwigFunction("prismic_has_text", [$this->richTextRenderer, "hasText"]),
			new TwigFunction("prismic_rich_text", [$this->richTextRenderer, "render"], ["is_safe" => ["html"]]),
			new TwigFunction("prismic_image_tag", [$this->imageBuilder, "buildImage"], ["is_safe" => ["html"]]),
			new TwigFunction("prismic_link_url", [$this->linkRenderer, "getUrl"]),
		];
	}
}
