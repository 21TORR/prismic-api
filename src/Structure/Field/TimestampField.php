<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;

/**
 * @see https://prismic.io/docs/core-concepts/timestamp
 */
final class TimestampField extends InputField
{
	use ValueValidationTrait;
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
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $data, [
			new Assert\Type("string"),
			new Assert\DateTime(
				format: \DateTimeInterface::ATOM,
			),
			$this->required ? new Assert\NotNull() : null,
		]);
	}
}
