<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Transform\DataTransformer;
use Torr\PrismicApi\Validation\DataValidator;
use Torr\PrismicApi\Visitor\DataVisitorInterface;

/**
 * @see https://prismic.io/docs/core-concepts/boolean
 */
final class BooleanField extends InputField
{
	private const TYPE_KEY = "Boolean";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		bool $defaultValue = false,
		?string $placeholderFalse = null,
		?string $placeholderTrue = null,
		private readonly bool $required = false,
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
	public function validateData (DataValidator $validator, array $path, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $path, $data, [
			new Assert\Type("bool"),
			$this->required ? new Assert\NotNull() : null,
		]);
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
