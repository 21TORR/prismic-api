<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data\Document;

use Torr\PrismicApi\Data\Dataset;
use Torr\PrismicApi\Exception\Data\InvalidDataStructureException;

/**
 * A top level
 */
abstract class Document extends Dataset
{
	protected DocumentAttributes $attributes;

	/**
	 */
	public function __construct (array $data)
	{
		$itemData = $data["data"] ?? null;

		if (!\is_array($itemData))
		{
			throw new InvalidDataStructureException(static::class, null, "No nested data key.");
		}

		parent::__construct($itemData);
		$this->attributes = new DocumentAttributes($data);
	}

	/**
	 */
	public function getAttributes () : DocumentAttributes
	{
		return $this->attributes;
	}
}
