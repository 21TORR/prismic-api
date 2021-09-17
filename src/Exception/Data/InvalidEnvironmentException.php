<?php
declare(strict_types=1);

namespace Torr\PrismicApi\Exception\Data;

use Torr\PrismicApi\Exception\PrismicApiException;

final class InvalidEnvironmentException extends \InvalidArgumentException implements PrismicApiException
{
}
