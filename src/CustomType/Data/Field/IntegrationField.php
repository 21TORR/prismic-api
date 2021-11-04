<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

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
		parent::__construct(self::TYPE_KEY, $this->filterOptionalFields([
			"label" => $label,
			"catalog" => $catalog,
			"placeholder" => $placeholder,
		]));
	}
}
