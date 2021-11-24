<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\CustomType\Helper\FilterFieldsHelper;

/**
 * @see https://prismic.io/docs/core-concepts/timestamp
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
		private bool $required = false,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
			"default" => $default,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function getValidationConstraints () : array
	{
		$constraints = [
			new Assert\Type("string"),
			new Assert\DateTime(
				format: \DateTimeInterface::ATOM,
			),
		];

		if ($this->required)
		{
			$constraints[] = new Assert\NotNull();
		}

		return $constraints;
	}
}
