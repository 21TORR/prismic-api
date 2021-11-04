<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Slice;

use Torr\PrismicApi\CustomType\Data\Field\InputField;
use Torr\PrismicApi\CustomType\Data\PrismicTypeInterface;

/**
 * @see https://prismic.io/docs/core-concepts/slices
 */
final class Slice implements PrismicTypeInterface
{
	/**
	 * @param array<string, InputField> $fields
	 * @param array<string, InputField> $repeatedFields
	 */
	public function __construct (
		private string $label,
		private array $fields,
		private array $repeatedFields,
		private ?string $description = null,
		private ?string $icon = null,
	)
	{

	}

	/**
	 */
	public function toArray () : array
	{
		return [
			"type" => "Slice",
			"fieldset" => $this->label,
			"description" => $this->description,
			"icon" => $this->icon,
			"non-repeat" => $this->fields,
			"repeat" => $this->repeatedFields,
		];
	}
}
