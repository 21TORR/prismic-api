<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Transform\DataTransformer;
use Torr\PrismicApi\Validation\DataValidator;

/**
 * @see https://prismic.io/docs/core-concepts/timestamp
 */
final class TimestampField extends InputField
{
	private const TYPE_KEY = "Timestamp";
	public const DEFAULT_NOW = "now";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $placeholder = null,
		?string $default = null,
		private readonly bool $required = false,
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
	public function validateData (DataValidator $validator, array $path, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $path, $data, [
			new Assert\Type("string"),
			new Assert\DateTime(
				format: \DateTimeInterface::ATOM,
			),
			$this->required ? new Assert\NotNull() : null,
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function transformValue (mixed $data, DataTransformer $dataTransformer) : ?string
	{
		return $data;
	}
}
