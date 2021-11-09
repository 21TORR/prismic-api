<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

use Torr\PrismicApi\CustomType\Helper\FilterFieldsHelper;

/**
 * @see https://prismic.io/docs/core-concepts/select
 */
final class SelectField extends InputField
{
	private const TYPE_KEY = "Select";


	/**
	 * @inheritDoc
	 *
	 * @param string[] $options
	 */
	public function __construct (
		string $label,
		array $options,
		?string $default_value = null,
		?string $placeholder = null,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
			"options" => $options,
			"default_value" => $default_value,
		]));
	}
}
