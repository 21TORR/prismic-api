<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;

/**
 * @see https://prismic.io/docs/core-concepts/date
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
		private bool $required = false,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
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
			new Assert\Date(),
		];

		if ($this->required)
		{
			$constraints[] = new Assert\NotNull();
		}

		return $constraints;
	}
}
