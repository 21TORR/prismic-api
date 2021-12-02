<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Torr\PrismicApi\Data\Value\DocumentLinkValue;
use Torr\PrismicApi\Data\Value\ImageValue;
use Torr\PrismicApi\Exception\Structure\InvalidTypeDefinitionException;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Transform\FieldValueTransformer;

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
		private ?string $select = self::SELECT_ALL,
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
	public function getValidationConstraints () : array
	{
		// @todo add validation
		return [];
	}


	/**
	 * @inheritDoc
	 */
	public function transformValue (mixed $data, FieldValueTransformer $valueTransformer) : mixed
	{
		$type = $data["link_type"] ?? null;
		$kind = $data["kind"] ?? null;

		if ("Web" === $type)
		{
			return $data["url"] ?? null;
		}

		if ("Media" === $type)
		{
			// return as an image, if specifically an image was asked for
			// @todo always return it this way (and use ImageValue or FileValue)
			if ("image" === $kind)
			{
				return new ImageValue(
					$data["url"],
					(int) $data["width"],
					(int) $data["height"],
					$data["name"],
				);
			}

			return $data["url"] ?? null;
		}

		if ("Document" === $type && \is_string($data["id"]))
		{
			return new DocumentLinkValue(
				$data["id"],
				$data["type"] ?? null,
			);
		}

		return null;
	}
}
