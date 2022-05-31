<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Torr\PrismicApi\Data\Value\DocumentLinkValue;
use Torr\PrismicApi\Data\Value\ImageValue;
use Torr\PrismicApi\Exception\Structure\InvalidTypeDefinitionException;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Transform\DataTransformer;
use Torr\PrismicApi\Validation\DataValidator;
use Torr\PrismicApi\Visitor\DataVisitorInterface;

/**
 * @see https://prismic.io/docs/core-concepts/link-content-relationship
 */
final class LinkField extends InputField
{
	private const TYPE_KEY = "Link";
	/**
	 * What the user can select
	 */
	public const SELECT_ALL = null;
	public const SELECT_WEB = "web";
	public const SELECT_MEDIA = "media";
	public const SELECT_DOCUMENT = "document";



	/**
	 * @inheritDoc
	 *
	 * @param string[] $customTypes
	 * @param string[] $tags
	 */
	public function __construct (
		string $label,
		private readonly ?string $select = self::SELECT_ALL,
		?string $placeholder = null,
		?array $customTypes = null,
		?array $tags = null,
	)
	{
		$hasDocumentFilter = !empty($customTypes) || !empty($tags);
		$selectsDocuments = self::SELECT_ALL === $this->select || self::SELECT_DOCUMENT === $this->select;

		if ($hasDocumentFilter && !$selectsDocuments)
		{
			throw new InvalidTypeDefinitionException("Can't filter for documents if not actually selecting documents");
		}


		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
			"select" => $this->select,
			"customtypes" => $customTypes,
			"tags" => $tags,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function validateData (
		DataValidator $validator,
		array $path,
		mixed $data,
	) : void
	{
		// @todo add validation
	}

	/**
	 * @inheritDoc
	 */
	public function transformValue (
		mixed $data,
		DataTransformer $dataTransformer,
		?DataVisitorInterface $dataVisitor = null,
	) : string|ImageValue|DocumentLinkValue|null
	{
		$type = $data["link_type"] ?? null;
		$kind = $data["kind"] ?? null;

		if ("Web" === $type)
		{
			return parent::transformValue(
				$dataTransformer->rewriteUrl($data["url"] ?? null),
				$dataTransformer,
				$dataVisitor,
			);
		}

		if ("Media" === $type)
		{
			// return as an image, if specifically an image was asked for
			// @todo always return it this way (and use ImageValue or FileValue)
			if ("image" === $kind)
			{
				return parent::transformValue(
					new ImageValue(
						$data["url"],
						(int) $data["width"],
						(int) $data["height"],
						$data["name"],
					),
					$dataTransformer,
					$dataVisitor,
				);
			}

			return parent::transformValue(
				$dataTransformer->rewriteUrl($data["url"] ?? null),
				$dataTransformer,
				$dataVisitor,
			);
		}

		if ("Document" === $type && \is_string($data["id"] ?? null))
		{
			return parent::transformValue(
				new DocumentLinkValue(
					$data["id"],
					$data["type"] ?? null,
					$data["lang"],
				),
				$dataTransformer,
				$dataVisitor,
			);
		}

		return parent::transformValue(
			null,
			$dataTransformer,
			$dataVisitor,
		);
	}
}
