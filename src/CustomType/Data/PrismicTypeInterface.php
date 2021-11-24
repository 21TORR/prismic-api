<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data;

use Symfony\Component\Validator\Constraint;
use Torr\PrismicApi\Transform\FieldValueTransformer;

/**
 * @internal
 */
interface PrismicTypeInterface
{
	/**
	 * Transforms the type to an array
	 */
	public function toArray () : array;

	/**
	 * @return Constraint[]
	 */
	public function getValidationConstraints() : array;

	/**
	 * Receives the prismic data for the given field and transforms it for better usage
	 */
	public function transformValue (mixed $data, FieldValueTransformer $valueTransformer) : mixed;
}
