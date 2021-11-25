<?php declare(strict_types=1);

namespace Torr\PrismicApi\Exception\Document;

use Torr\PrismicApi\Exception\PrismicApiException;

final class MissingDocumentDefinitionException extends \InvalidArgumentException implements PrismicApiException
{
}
