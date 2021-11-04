<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

/**
 * @link https://prismic.io/docs/core-concepts/uid
 */
final class UidField extends InputField
{
	private const TYPE_KEY = "UID";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $placeholder = null
	)
	{
		parent::__construct(self::TYPE_KEY, [
			"label" => $label,
			"placeholder" => $placeholder,
		]);
	}
}
