<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data;

/**
 * A single tab in the prismic editing UI / data structure
 */
final class TypeTab
{
	/**
	 * @param array<string, InputField> $fields
	 */
	public function __construct (
		private string $label,
		private array $fields,
	)
	{
	}

	/**
	 */
	public function getLabel () : string
	{
		return $this->label;
	}


	/**
	 *
	 */
	public function toArray () : array
	{
		$result = [];

		foreach ($this->fields as $key => $field)
		{
			$result[$key] = $field->toArray();
		}

		return $result;
	}
}
