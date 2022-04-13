<?php declare(strict_types=1);

namespace Torr\PrismicApi\Visitor\Translation;

use Torr\PrismicApi\Data\Value\DocumentLinkValue;
use Torr\PrismicApi\Structure\Field\LinkField;
use Torr\PrismicApi\Structure\Field\RichTextField;
use Torr\PrismicApi\Structure\PrismicTypeInterface;
use Torr\PrismicApi\Visitor\DataVisitorInterface;

class TranslationCheckVisitor implements DataVisitorInterface
{
	/** @var TranslationCheckIssue[] */
	private array $issue = [];

	public function __construct(
		private readonly string $documentLocale,
	) {}


	/**
	 */
	public function onDataVisit(
		PrismicTypeInterface $field,
		mixed $data,
	) : void
	{
		if ($field instanceof RichTextField)
		{
			foreach ($data as $item)
			{
				if (!empty($item))
				{
					$check = true;

					foreach ($item["spans"] as $span) {
						if ("hyperlink" === $span["type"])
						{
							if($span["data"]["url"] instanceof DocumentLinkValue && $span["data"]["url"]->getTargetLocale() !== $this->documentLocale)
							{
								$this->issue[] = new TranslationCheckIssue(
									"ERROR",
									"RTE - Wrong locale in Link.",
									$field,
								);
							}
						}

						if (!empty($span["type"]) && $check)
						{
							$check = false;
							$this->issue[] = new TranslationCheckIssue(
								"INFO",
								"RTE has span.",
								$field,
							);
						}
					}
				}
			}
			return;
		}

		if ($field instanceof LinkField && $data instanceof DocumentLinkValue)
		{
			if ($data->getTargetLocale() !== $this->documentLocale)
			{
				$this->issue[] = new TranslationCheckIssue(
					"ERROR",
					"Document with wrong locale.",
					$field,
				);
			}
		}
		// do antother field check
	}

	/**
	 * @return TranslationCheckIssue[]
	 */
	public function getIssue() : array
	{
		return $this->issue;
	}

}
