<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Transform\DataTransformer;
use Torr\PrismicApi\Validation\DataValidator;
use Torr\PrismicApi\Visitor\DataVisitorInterface;

/**
 * @see https://prismic.io/docs/core-concepts/number
 */
final class NumberField extends InputField
{
	private const TYPE_KEY = "Number";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $placeholder = null,
		private readonly ?int $min = null,
		private readonly ?int $max = null,
		private readonly bool $required = false,
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
	public function validateData (DataValidator $validator, array $path, mixed $data) : void
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

		$this->ensureDataIsValid($validator, $path, $data, $constraints);
	}

	/**
	 * @inheritDoc
	 *
	 * @template T
	 *
	 * @param T $data
	 *
	 * @return T
	 */
	public function transformValue (
		mixed $data,
		DataTransformer $dataTransformer,
		?DataVisitorInterface $dataVisitor = null,
	) : mixed
	{
		$dataVisitor?->onDataVisit($this, $data);

		return $data;
	}
}
