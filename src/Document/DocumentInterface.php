<?php declare(strict_types=1);

namespace Torr\PrismicApi\Document;

/**
 * You should not use this interface, but instead always extend {@see Document}
 *
 * @internal
 */
interface DocumentInterface
{
	/**
	 * ID of the prismic type
	 */
	public static function getDocumentTypeId () : string;
}
