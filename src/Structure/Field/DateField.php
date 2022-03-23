<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;

/**
 * @see https://prismic.io/docs/core-concepts/date
 */
final class DateField extends InputField
{
	use ValueValidationTrait;
	private const TYPE_KEY = "Date";
	public const DEFAULT_NOW = "now";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $default = null,
		private readonly bool $required = false,
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
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $data, [
			new Assert\Type("string"),
			new Assert\Date(),
			$this->required ? new Assert\NotNull() : null,
		]);
	}
}
