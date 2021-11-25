<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure;

use Symfony\Component\Validator\Constraint;
use Torr\PrismicApi\Transform\FieldValueTransformer;

/**
 * @internal
 */
interface PrismicTypeInterface
{
	/**
	 * Transforms the type to the type definition required for the prismic API
	 */
	public function formatTypeDefinition () : array;

	/**
	 * @return Constraint[]
	 */
	public function getValidationConstraints() : array;

	/**
	 * Receives the prismic data for the given field and transforms it for better usage
	 */
	public function transformValue (mixed $data, FieldValueTransformer $valueTransformer) : mixed;
}
