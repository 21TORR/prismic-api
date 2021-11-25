<?php declare(strict_types=1);

namespace Torr\PrismicApi\Exception\Structure;

use Torr\PrismicApi\Exception\PrismicApiException;

final class InvalidTypeDefinitionException extends \InvalidArgumentException implements PrismicApiException
{
}
