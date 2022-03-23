<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;

/**
 * @see https://prismic.io/docs/core-concepts/select
 */
final class SelectField extends InputField
{
	use ValueValidationTrait;
	private const TYPE_KEY = "Select";


	/**
	 * @inheritDoc
	 *
	 * @param string[] $options
	 */
	public function __construct (
		string $label,
		private array $options,
		?string $default_value = null,
		?string $placeholder = null,
		private bool $required = false,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
			"options" => $this->options,
			"default_value" => $default_value,
		]));
	}


	/**
	 * @inheritDoc
	 */
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $data, [
			new Assert\Type("string"),
			new Assert\Choice(
				choices: $this->options,
			),
			$this->required ? new Assert\NotNull() : null,
		]);
	}
}
