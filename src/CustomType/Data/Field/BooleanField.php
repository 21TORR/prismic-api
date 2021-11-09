<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

use Torr\PrismicApi\CustomType\Helper\FilterFieldsHelper;

/**
 * @see https://prismic.io/docs/core-concepts/boolean
 */
final class BooleanField extends InputField
{
	private const TYPE_KEY = "Boolean";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		bool $defaultValue = false,
		?string $placeholderFalse = null,
		?string $placeholderTrue = null,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder_false" => $placeholderFalse,
			"placeholder_true" => $placeholderTrue,
			"default_value" => $defaultValue,
		]));
	}
}
