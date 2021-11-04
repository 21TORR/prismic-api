<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

/**
 * @link https://prismic.io/docs/core-concepts/timestamp
 */
final class TimestampField extends InputField
{
	private const TYPE_KEY = "Timestamp";
	public const DEFAULT_NOW = "now";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $placeholder = null,
		?string $default = null,
	)
	{
		parent::__construct(self::TYPE_KEY, $this->filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
			"default" => $default,
		]));
	}
}
