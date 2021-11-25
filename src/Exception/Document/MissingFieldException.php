<?php declare(strict_types=1);

namespace Torr\PrismicApi\Exception\Document;

use Torr\PrismicApi\Exception\PrismicApiException;

final class MissingFieldException extends \InvalidArgumentException implements PrismicApiException
{
}
