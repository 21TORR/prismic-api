<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\CustomType\Helper\FilterFieldsHelper;

/**
 * @see https://prismic.io/docs/core-concepts/color
 */
final class ColorField extends InputField
{
	private const TYPE_KEY = "Color";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		private bool $required = false,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function getValidationConstraints () : array
	{
		$constraints = [
			new Assert\Type("string"),
			// @todo add CssColor validation as soon as Symfony 5.4 is released
		];

		if ($this->required)
		{
			$constraints[] = new Assert\NotNull();
		}

		return $constraints;
	}
}
