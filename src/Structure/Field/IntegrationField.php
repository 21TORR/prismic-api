<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;

/**
 * @see https://prismic.io/docs/core-concepts/integration-fields
 */
final class IntegrationField extends InputField
{
	private const TYPE_KEY = "IntegrationFields";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		string $catalog,
		?string $placeholder = null,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"catalog" => $catalog,
			"placeholder" => $placeholder,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function getValidationConstraints () : array
	{
		// @todo add validation
		return [];
	}
}