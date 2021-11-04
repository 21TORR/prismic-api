<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

use Torr\PrismicApi\CustomType\Helper\KeyedMapHelper;

/**
 * @link https://prismic.io/docs/core-concepts/group
 */
final class GroupField extends InputField
{
	private const TYPE_KEY = "Group";


	/**
	 * @inheritDoc
	 *
	 * @param array<string, InputField> $fields
	 */
	public function __construct (
		string $label,
		array $fields,
		?bool $repeat = false,
	)
	{
		parent::__construct(self::TYPE_KEY, $this->filterOptionalFields([
			"label" => $label,
			"repeat" => $repeat,
			"fields" => KeyedMapHelper::transformKeyedListOfTypes($fields),
		]));
	}
}
