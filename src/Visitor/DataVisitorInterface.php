<?php declare(strict_types=1);

namespace Torr\PrismicApi\Visitor;

use Torr\PrismicApi\Structure\PrismicTypeInterface;

interface DataVisitorInterface
{
	/**
	 * If a data visitor is given, it will be called for every field with the field definition and data.
	 */
	public function onDataVisit (
		PrismicTypeInterface $field,
		mixed $data,
	) : void;
}
