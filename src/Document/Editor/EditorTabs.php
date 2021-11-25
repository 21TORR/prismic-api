<?php declare(strict_types=1);

namespace Torr\PrismicApi\Document\Editor;

use Torr\PrismicApi\CustomType\Data\Field\InputField;
use Torr\PrismicApi\CustomType\Data\Slice\SliceZone;
use Torr\PrismicApi\CustomType\Helper\KeyedMapHelper;
use Torr\PrismicApi\Exception\Document\InvalidDocumentStructureException;
use Torr\PrismicApi\Exception\Document\MissingFieldException;

final class EditorTabs
{
	/** array<string, array<string, InputField|SliceZone>> */
	private array $tabs = [];
	/** array<string, InputField|SliceZone> */
	private array $fields = [];


	/**
	 * Adds a tab to the editor
	 *
	 * @param array<string, InputField|SliceZone> $fields
	 *
	 * @return $this
	 */
	public function addTab (string $label, array $fields) : self
	{
		if (empty($fields))
		{
			throw new InvalidDocumentStructureException(\sprintf(
				"Can't add empty tab '%s'.",
				$label,
			));
		}

		foreach ($fields as $key => $field)
		{
			if (\array_key_exists($key, $this->fields))
			{
				throw new InvalidDocumentStructureException(\sprintf(
					"Can't register multiple fields with key '%s'.",
					$key,
				));
			}

			$this->fields[$key] = $field;
		}

		if (\array_key_exists($label, $this->tabs))
		{
			throw new InvalidDocumentStructureException(\sprintf(
				"Can't register multiple tabs with label '%s'.",
				$label,
			));
		}

		$this->tabs[$label] = $fields;
		return $this;
	}

	/**
	 */
	public function getByKey (string $key) : InputField|SliceZone
	{
		$field = $this->fields[$key] ?? null;

		if (null === $field)
		{
			throw new MissingFieldException(\sprintf(
				"No field found with key '%s'",
				$key,
			));
		}

		return $field;
	}

	/**
	 */
	public function getSliceZoneByKey (string $key) : SliceZone
	{
		$zone = $this->getByKey($key);

		if (!$zone instanceof SliceZone)
		{
			throw new MissingFieldException(\sprintf(
				"Field with key '%s' must be a slice zone, but is an input field",
				$key,
			));
		}

		return $zone;
	}

	/**
	 */
	public function getFields () : array
	{
		return $this->fields;
	}

	/**
	 */
	public function getTypesDefinition () : array
	{
		$result = [];

		foreach ($this->tabs as $label => $fields)
		{
			$result[$label] = KeyedMapHelper::transformKeyedListOfTypes($fields);
		}

		return $result;
	}
}
