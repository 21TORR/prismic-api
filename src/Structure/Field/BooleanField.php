<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;

/**
 * @see https://prismic.io/docs/core-concepts/boolean
 */
final class BooleanField extends InputField
{
	use ValueValidationTrait;
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
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $data, [
			new Assert\Type("bool"),
			$this->required ? new Assert\NotNull() : null,
		]);
	}
}
