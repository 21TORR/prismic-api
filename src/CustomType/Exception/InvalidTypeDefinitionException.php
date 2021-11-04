<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Exception;

use Torr\PrismicApi\Exception\PrismicApiException;

final class InvalidTypeDefinitionException extends \InvalidArgumentException implements PrismicApiException
{
}
