<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;

/**
 * This is called "Key Text" in the Prismic UI
 *
 * @see https://prismic.io/docs/core-concepts/key-text
 */
final class TextField extends InputField
{
	use ValueValidationTrait;
	private const TYPE_KEY = "Text";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $placeholder = null,
		private bool $required = false,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $data, [
			new Assert\Type("string"),
			$this->required ? new Assert\NotNull() : null,
		]);
	}
}
