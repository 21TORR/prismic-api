<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

/**
 * @see https://prismic.io/docs/core-concepts/embed
 */
final class EmbedField extends InputField
{
	private const TYPE_KEY = "Embed";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $placeholder = null,
	)
	{
		parent::__construct(self::TYPE_KEY, $this->filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
		]));
	}
}
