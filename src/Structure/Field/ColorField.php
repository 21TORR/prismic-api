<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Transform\DataTransformer;
use Torr\PrismicApi\Validation\DataValidator;

/**
 * @see https://prismic.io/docs/core-concepts/color
 */
final class ColorField extends InputField
{
	private const TYPE_KEY = "Color";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		private readonly bool $required = false,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function validateData (DataValidator $validator, array $path, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $path, $data, [
			new Assert\Type("string"),
			new Assert\CssColor([
				Assert\CssColor::HEX_LONG,
				Assert\CssColor::HEX_SHORT,
			]),
			// @todo add CssColor validation as soon as Symfony 5.4 is released
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
