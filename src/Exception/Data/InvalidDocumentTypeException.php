<?php declare(strict_types=1);

namespace Torr\PrismicApi\Exception\Data;

use Torr\PrismicApi\Exception\PrismicApiException;

final class InvalidDocumentTypeException extends \InvalidArgumentException implements PrismicApiException
{
}
