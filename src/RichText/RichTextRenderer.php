<?php
declare(strict_types=1);

namespace Torr\PrismicApi\RichText;

use Torr\HtmlBuilder\Builder\HtmlBuilder;
use Torr\HtmlBuilder\Node\HtmlElement;

final class RichTextRenderer
{
	public function render (array $text, array $sectionAttributes = []) : string
	{
		$builder = new HtmlBuilder();

		return \implode(
			"",
			\array_map(
				static function (array $section) use ($builder, $sectionAttributes)
				{
					$element = match ($section["type"])
					{
						"paragraph" => new HtmlElement("p", $sectionAttributes, [$section["text"]]),
						"heading1" => new HtmlElement("h1", $sectionAttributes, [$section["text"]]),
						"heading2" => new HtmlElement("h2", $sectionAttributes, [$section["text"]]),
						"heading3" => new HtmlElement("h3", $sectionAttributes, [$section["text"]]),
						"heading4" => new HtmlElement("h4", $sectionAttributes, [$section["text"]]),
						"heading5" => new HtmlElement("h5", $sectionAttributes, [$section["text"]]),
						"heading6" => new HtmlElement("h6", $sectionAttributes, [$section["text"]]),
						default => new HtmlElement("div", $sectionAttributes, [$section["text"]]),
					};

					return $builder->build($element);
				},
				$text
			)
		);
	}


	public function hasText (array $text) : bool
	{
		foreach ($text as $section)
		{
			if ("" !== $section["text"])
			{
				return true;
			}
		}

		return false;
	}
}
