<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

/**
 * @link https://prismic.io/docs/core-concepts/rich-text-title
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
		?int $maxImageWidth = null,
		?int $maxImageHeight = null,
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
		$imageConstraints = null;

		if (null !== $maxImageWidth || null !== $maxImageHeight)
		{
			$imageConstraints = $this->filterOptionalFields([
				"width" => $maxImageWidth,
				"height" => $maxImageHeight,
			]);
		}

		parent::__construct(self::TYPE_KEY, $this->filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
			$stylesKey => $styles,
			"allowTargetBlank" => $allowTargetBlankForLinks,
			"imageConstraints" => $imageConstraints,
		]));
	}
}
