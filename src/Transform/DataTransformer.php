<?php declare(strict_types=1);

namespace Torr\PrismicApi\Transform;

use Torr\PrismicApi\Data\Value\DocumentLinkValue;
use Torr\PrismicApi\Structure\Field\InputField;
use Torr\PrismicApi\Structure\Slice\Slice;
use Torr\PrismicApi\Structure\Slice\SliceZone;
use Torr\PrismicApi\Transform\Link\UrlRewriterInterface;
use Torr\PrismicApi\Transform\Slice\SliceExtraDataGeneratorInterface;

final class DataTransformer
{
	public function __construct (
		/** @var iterable<SliceExtraDataGeneratorInterface> */
		private readonly iterable $sliceExtraDataGenerators,
		/** @var iterable<UrlRewriterInterface> */
		private readonly iterable $urlRewriters,
	) {}

	/**
	 */
	public function transformValue (
		array $data,
		InputField|SliceZone $field,
	) : mixed
	{
		return $field->transformValue($data, $this);
	}


	/**
	 * Transforms rich text.
	 * It mainly resolves internal links to their URL.
	 */
	public function transformRichText (array $data) : array
	{
		foreach ($data as $index => $paragraph)
		{
			$spans = [];

			foreach ($paragraph["spans"] as $span)
			{
				$type = $span["type"];
				$linkType = $span["data"]["link_type"] ?? null;

				if ("hyperlink" === $type && "Document" === $linkType)
				{
					$spans[] = $this->rewriteDocumentLinkToUrl($span);
					continue;
				}

				if ("hyperlink" === $type && "Web" === $linkType)
				{
					$span["data"]["url"] = $this->rewriteUrl($span["data"]["url"] ?? null);
					$spans[] = $span;
					continue;
				}

				$spans[] = $span;
			}

			$data[$index]["spans"] = $spans;
		}

		return $data;
	}

	/**
	 * Rewrites the given document-type span to a hyperlink-type span
	 */
	private function rewriteDocumentLinkToUrl (array $span) : array
	{
		// this will always be an internal link, so no target => _blank
		$type = $span["data"]["type"];
		$lang = $span["data"]["lang"];

		// if broken link, just leave it untouched
		if ("broken_type" === $type || null === $lang)
		{
			return $span;
		}

		$span["data"] = [
			"link_type" => "Web",
			"url" => new DocumentLinkValue(
				$span["data"]["id"],
				$type,
				$lang,
			),
		];

		return $span;
	}

	/**
	 */
	public function rewriteUrl (?string $url) : ?string
	{
		/** @var UrlRewriterInterface $urlRewriter */
		foreach ($this->urlRewriters as $urlRewriter)
		{
			$url = $urlRewriter->rewriteUrl($url);
		}

		return $url;
	}

	/**
	 * Generates the extra data for the given slice
	 */
	public function generateExtraDataForSlice (Slice $slice) : array
	{
		$result = [];

		/** @var SliceExtraDataGeneratorInterface $dataGenerator */
		foreach ($this->sliceExtraDataGenerators as $dataGenerator)
		{
			$result = $dataGenerator->appendExtraData($slice, $result);
		}

		return $result;
	}
}
