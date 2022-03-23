<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure;

use Torr\PrismicApi\Transform\DataTransformer;
use Torr\PrismicApi\Validation\DataValidator;

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
	 * Validates the data for this field, as it was sent by Prismic.
	 */
	public function validateData (DataValidator $validator, array $path, mixed $data) : void;

	/**
	 * Receives the prismic data for the given field and transforms it for better usage
	 */
	public function transformValue (mixed $data, DataTransformer $dataTransformer) : mixed;
}
