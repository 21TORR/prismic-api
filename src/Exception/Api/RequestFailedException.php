<?php
declare(strict_types=1);

namespace Torr\PrismicApi\Exception\Api;

use Torr\PrismicApi\Exception\PrismicApiException;

final class RequestFailedException extends \RuntimeException implements PrismicApiException
{
}
