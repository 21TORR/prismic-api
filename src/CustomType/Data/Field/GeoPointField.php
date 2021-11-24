<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\CustomType\Helper\FilterFieldsHelper;

/**
 * @see https://prismic.io/docs/core-concepts/geopoint
 */
final class GeoPointField extends InputField
{
	private const TYPE_KEY = "GeoPoint";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		private bool $required = false,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function getValidationConstraints () : array
	{
		$constraints = [
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
		];

		if ($this->required)
		{
			$constraints[] = new Assert\NotNull();
		}

		return $constraints;
	}
}
