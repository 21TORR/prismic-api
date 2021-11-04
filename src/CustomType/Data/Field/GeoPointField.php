<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

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
	)
	{
		parent::__construct(self::TYPE_KEY, $this->filterOptionalFields([
			"label" => $label,
		]));
	}
}
