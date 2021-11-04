<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

/**
 * @link https://prismic.io/docs/core-concepts/rich-text-title
 */
class HeadingField extends RichTextField
{
	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		array $headlineLevels = RichTextField::HEADINGS,
		?string $placeholder = null,
	)
	{
		parent::__construct(
			label: $label,
			styles: $headlineLevels,
			allowsMultipleLines: false,
			placeholder: $placeholder,
		);
	}
}
