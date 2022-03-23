<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Validation\DataValidator;

/**
 * @see https://prismic.io/docs/core-concepts/select
 */
final class SelectField extends InputField
{
	private const TYPE_KEY = "Select";


	/**
	 * @inheritDoc
	 *
	 * @param string[] $options
	 */
	public function __construct (
		string $label,
		private readonly array $options,
		?string $default_value = null,
		?string $placeholder = null,
		private readonly bool $required = false,
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
	public function validateData (DataValidator $validator, array $path, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $path, $data, [
			new Assert\Type("string"),
			new Assert\Choice(
				choices: $this->options,
			),
			$this->required ? new Assert\NotNull() : null,
		]);
	}
}
