<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Validation\DataValidator;

/**
 * @see https://prismic.io/docs/core-concepts/uid
 */
final class UidField extends InputField
{
	private const TYPE_KEY = "UID";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $placeholder = null,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function validateData (DataValidator $validator, array $path, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $path, $data, [
			new Assert\Type("string"),
		]);
	}
}
