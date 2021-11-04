<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

/**
 * @link https://prismic.io/docs/core-concepts/number
 */
final class NumberField extends InputField
{
	private const TYPE_KEY = "Number";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $placeholder = null,
		?int $min = null,
		?int $max = null,
	)
	{
		parent::__construct(self::TYPE_KEY, [
			"label" => $label,
			"placeholder" => $placeholder,
			"min" => $min,
			"max" => $max,
		]);
	}
}
