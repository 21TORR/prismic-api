<?php declare(strict_types=1);

namespace Torr\PrismicApi\Visitor\Translation;

use Torr\PrismicApi\Data\Value\DocumentLinkValue;
use Torr\PrismicApi\Structure\Field\LinkField;
use Torr\PrismicApi\Structure\Field\RichTextField;
use Torr\PrismicApi\Structure\PrismicTypeInterface;
use Torr\PrismicApi\Visitor\Check\IssueSeverity;
use Torr\PrismicApi\Visitor\DataVisitorInterface;

class TranslationCheckVisitor implements DataVisitorInterface
{
	/** @var TranslationCheckIssue[] */
	private array $issues = [];

	/**
	 */
	public function __construct (
		private readonly string $documentLocale,
	)
	{
	}

	/**
	 */
	public function onDataVisit (
		PrismicTypeInterface $field,
		mixed $data,
	) : void
	{
		switch (true)
		{
			case $field instanceof LinkField:
				$this->checkLinkField($field, $data);
				break;

			case $field instanceof RichTextField:
				$this->checkRteField($field, $data);
				break;
		}
	}


	/**
	 */
	private function checkRteField (RichTextField $field, mixed $data) : void
	{
		\assert(\is_array($data));

		foreach ($data as $paragraph)
		{
			if (empty($paragraph))
			{
				continue;
			}

			$spans = \array_column($paragraph["spans"], "type");

			if (!empty($spans))
			{
				$this->issues[] = new TranslationCheckIssue(
					IssueSeverity::INFO,
					\sprintf(
						"RTE has span(s) of type: %s",
					\implode(", ", \array_unique($spans))
					),
					$field,
				);
			}

			foreach ($paragraph["spans"] as $span)
			{
				if (
					"hyperlink" === $span["type"]
					&& $span["data"]["url"] instanceof DocumentLinkValue
				)
				{
					$this->validateDocumentLinkValue($field, $span["data"]["url"], "RTE - Wrong locale in Link.");
				}
			}
		}
	}


	/**
	 */
	private function checkLinkField (LinkField $field, mixed $data) : void
	{
		if (!$data instanceof DocumentLinkValue)
		{
			return;
		}

		$this->validateDocumentLinkValue($field, $data, "Linked to a document in another locale.");
	}


	/**
	 */
	private function validateDocumentLinkValue (
		PrismicTypeInterface $field,
		DocumentLinkValue $documentLink,
		string $message,
	) : void
	{
		if ($documentLink->getTargetLocale() !== $this->documentLocale)
		{
			$this->issues[] = new TranslationCheckIssue(
				IssueSeverity::ERROR,
				$message,
				$field,
			);
		}
	}

	/**
	 * @return TranslationCheckIssue[]
	 */
	public function getIssues () : array
	{
		return $this->issues;
	}

}
