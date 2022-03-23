<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;

/**
 * @see https://prismic.io/docs/core-concepts/color
 */
final class ColorField extends InputField
{
	use ValueValidationTrait;
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
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $data, [
			new Assert\Type("string"),
			// @todo add CssColor validation as soon as Symfony 5.4 is released
			$this->required ? new Assert\NotNull() : null,
		]);
	}
}
