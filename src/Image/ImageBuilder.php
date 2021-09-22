<?php declare(strict_types=1);

namespace Torr\PrismicApi\Image;

use Torr\HtmlBuilder\Builder\HtmlBuilder;
use Torr\HtmlBuilder\Node\HtmlElement;

final class ImageBuilder
{
	public function buildImage (array $image, array $attributes = []) : string
	{
		$attributes["src"] = $image["url"];
		$attributes["alt"] = $image["alt"] ?? "";
		$attributes["width"] = $image["dimensions"]["width"];
		$attributes["height"] = $image["dimensions"]["height"];

		$builder = new HtmlBuilder();
		return $builder->build(
			new HtmlElement("img", $attributes),
		);
	}
}
