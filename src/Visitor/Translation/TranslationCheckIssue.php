<?php declare(strict_types=1);

namespace Torr\PrismicApi\Visitor\Translation;

use Torr\PrismicApi\Structure\PrismicTypeInterface;
use Torr\PrismicApi\Visitor\Check\IssueSeverity;

final class TranslationCheckIssue
{
	public function __construct(
		private readonly IssueSeverity $severity,
		private readonly string $message,
		private readonly PrismicTypeInterface $field,
	) {}

	/**
	 */
	public function getSeverity() : IssueSeverity
	{
		return $this->severity;
	}

	/**
	 */
	public function getMessage() : string
	{
		return $this->message;
	}

	/**
	 */
	public function getField() : PrismicTypeInterface
	{
		return $this->field;
	}

}
