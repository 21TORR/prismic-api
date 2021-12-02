<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data;

use Torr\PrismicApi\Editor\EditorTabs;
use Torr\PrismicApi\Transform\FieldValueTransformer;

abstract class Document
{
	protected array $data;
	protected DocumentAttributes $attributes;

	/**
	 * You should never create an instance manually, always use {@see DocumentFactory} instead.
	 *
	 * @internal
	 */
	final public function __construct (
		array $data,
		protected EditorTabs $editorTabs,
	)
	{
		$this->data = $data["data"];
		$this->attributes = new DocumentAttributes($data);
	}


	/**
	 */
	public function getAttributes () : DocumentAttributes
	{
		return $this->attributes;
	}


	/**
	 */
	public function getId () : string
	{
		return $this->attributes->getId();
	}


	/**
	 * Transforms the slice zone with the given key
	 */
	protected function transformSliceZone (FieldValueTransformer $valueTransformer, string $key) : array
	{
		return $valueTransformer->transformValue(
			$this->data[$key] ?? [],
			$this->editorTabs->getSliceZoneByKey($key),
		);
	}


	/**
	 * Transforms the input field with the given key
	 */
	protected function transformField (FieldValueTransformer $valueTransformer, string $key) : mixed
	{
		return $valueTransformer->transformValue(
			$this->data[$key] ?? [],
			$this->editorTabs->getByKey($key),
		);
	}
}
