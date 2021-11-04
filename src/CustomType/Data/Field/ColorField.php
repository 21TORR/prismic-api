<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

/**
 * @link https://prismic.io/docs/core-concepts/color
 */
final class ColorField extends InputField
{
	private const TYPE_KEY = "Color";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
	)
	{
		parent::__construct(self::TYPE_KEY, $this->filterOptionalFields([
			"label" => $label,
		]));
	}
}