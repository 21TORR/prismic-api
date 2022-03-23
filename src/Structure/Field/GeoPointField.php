<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;

/**
 * @see https://prismic.io/docs/core-concepts/geopoint
 */
final class GeoPointField extends InputField
{
	use ValueValidationTrait;
	private const TYPE_KEY = "GeoPoint";


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
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $data, [
			new Assert\Type("array"),
			new Assert\Collection([
				"fields" => [
					"latitude" => [
						new Assert\NotNull(),
						new Assert\Type("float"),
					],
					"longitude" => [
						new Assert\NotNull(),
						new Assert\Type("float"),
					],
				],
				"allowExtraFields" => true,
				"allowMissingFields" => false,
			]),
			$this->required ? new Assert\NotNull() : null,
		]);
	}
}
