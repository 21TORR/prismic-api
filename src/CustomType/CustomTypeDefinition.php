<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType;

use Torr\PrismicApi\CustomType\Data\TypeTab;

interface CustomTypeDefinition
{
	/**
	 * Returns the ID of the custom type
	 */
	public function getId () : string;

	/**
	 * Returns the label for this type
	 */
	public function getLabel () : string;

	/**
	 * Returns whether an element with this type
	 */
	public function isRepeatable () : bool;

	/**
	 * Returns all tabs for this type
	 *
	 * @return TypeTab[]
	 */
	public function getTabs () : array;

	/**
	 * Returns whether this custom type is active
	 */
	public function isActive () : bool;
}
