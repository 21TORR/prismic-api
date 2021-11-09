<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

use Torr\PrismicApi\CustomType\Helper\FilterFieldsHelper;

/**
 * @see https://prismic.io/docs/core-concepts/color
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
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
		]));
	}
}
