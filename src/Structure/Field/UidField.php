<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;

/**
 * @see https://prismic.io/docs/core-concepts/uid
 */
final class UidField extends InputField
{
	use ValueValidationTrait;
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
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $data, [
			new Assert\NotNull(),
			new Assert\Type("string"),
		]);
	}
}
