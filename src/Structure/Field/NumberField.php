<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;

/**
 * @see https://prismic.io/docs/core-concepts/number
 */
final class NumberField extends InputField
{
	use ValueValidationTrait;
	private const TYPE_KEY = "Number";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $placeholder = null,
		private ?int $min = null,
		private ?int $max = null,
		private bool $required = false,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
			"min" => $this->min,
			"max" => $this->max,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		$constraints = [
			new Assert\Type("numeric"),
		];

		if ($this->required)
		{
			$constraints[] = new Assert\NotNull();
		}

		if (null !== $this->min || null !== $this->max)
		{
			$constraints[] = new Assert\Range(
				min: $this->min,
				max: $this->max,
			);
		}

		$this->ensureDataIsValid($validator, $data, $constraints);
	}
}
