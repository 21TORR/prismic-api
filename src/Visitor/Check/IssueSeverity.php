<?php declare(strict_types=1);

namespace Torr\PrismicApi\Visitor\Check;

enum IssueSeverity: string
{
	case INFO = "info";
	case ERROR = "error";
}
