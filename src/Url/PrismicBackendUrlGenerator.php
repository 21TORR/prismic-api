<?php declare(strict_types=1);

namespace Torr\PrismicApi\Url;

use Torr\PrismicApi\Data\Document;

final class PrismicBackendUrlGenerator
{
	/**
	 */
	public function __construct (
		private readonly string $repository,
	) {}


	/**
	 * Returns the link to the document admin
	 */
	public function linkToDocumentAdmin (Document $document) : string
	{
		return sprintf(
			"https://{$this->repository}.prismic.io/documents~b=working&c=published&l=%s/%s/",
			$document->getAttributes()->getLanguage(),
			$document->getId(),
		);
	}
}
