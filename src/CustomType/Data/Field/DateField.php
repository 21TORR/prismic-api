<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

/**
 * @link https://prismic.io/docs/core-concepts/date
 */
final class DateField extends InputField
{
	private const TYPE_KEY = "Date";
	public const DEFAULT_NOW = "now";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $default = null,
	)
	{
		parent::__construct(self::TYPE_KEY, [
			"label" => $label,
			"default" => $default,
		]);
	}
}
