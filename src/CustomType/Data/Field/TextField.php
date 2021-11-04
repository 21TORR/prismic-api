<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

/**
 * This is called "Key Text" in the Prismic UI
 *
 * @link https://prismic.io/docs/core-concepts/key-text
 */
final class TextField extends InputField
{
	private const TYPE_KEY = "Text";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $placeholder = null,
	)
	{
		parent::__construct(self::TYPE_KEY, [
			"label" => $label,
			"placeholder" => $placeholder,
		]);
	}
}
