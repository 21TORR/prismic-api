<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

use Torr\PrismicApi\CustomType\Data\Part\ImageConstraint;

/**
 * @see https://prismic.io/docs/core-concepts/rich-text-title
 */
class RichTextField extends InputField
{
	private const TYPE_KEY = "StructuredText";
	public const HEADINGS = [
		"heading1",
		"heading2",
		"heading3",
		"heading4",
		"heading5",
		"heading6",
	];
	public const INLINE_STYLE = [
		"strong",
		"em",
		"hyperlink",
	];
	public const PARAGRAPH_STYLE = [
		"paragraph",
		"preformatted",
		"list-item",
		"o-list-item",
		"image",
		"embed",
	];
	public const RTL = "rtl";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?array $styles = null,
		bool $allowsMultipleLines = true,
		bool $allowTargetBlankForLinks = true,
		?string $placeholder = null,
		?ImageConstraint $imageConstraint = null,
	)
	{
		if (null === $styles)
		{
			$styles = [
				...self::HEADINGS,
				...self::INLINE_STYLE,
				...self::PARAGRAPH_STYLE,
			];
		}

		$stylesKey = $allowsMultipleLines ? "multi" : "single";

		parent::__construct(self::TYPE_KEY, $this->filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
			$stylesKey => \implode(", ", $styles),
			"allowTargetBlank" => $allowTargetBlankForLinks,
			"imageConstraint" => $imageConstraint?->toArray(),
		]));
	}
}
