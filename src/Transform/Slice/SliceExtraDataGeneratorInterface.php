<?php declare(strict_types=1);

namespace Torr\PrismicApi\Transform\Slice;

use Torr\PrismicApi\Structure\Slice\Slice;

interface SliceExtraDataGeneratorInterface
{
	/**
	 * @param array $extraData The pre-generated extra data, that can get appended to
	 */
	public function appendExtraData (
		Slice $slice,
		array $extraData,
	) : array;
}
