<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;

/**
 * @see https://prismic.io/docs/core-concepts/boolean
 */
final class BooleanField extends InputField
{
	private const TYPE_KEY = "Boolean";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		bool $defaultValue = false,
		?string $placeholderFalse = null,
		?string $placeholderTrue = null,
		private bool $required = false,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder_false" => $placeholderFalse,
			"placeholder_true" => $placeholderTrue,
			"default_value" => $defaultValue,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function getValidationConstraints () : array
	{
		$constraints = [
			new Assert\Type("bool"),
		];

		if ($this->required)
		{
			$constraints[] = new Assert\NotNull();
		}

		return $constraints;
	}
}
