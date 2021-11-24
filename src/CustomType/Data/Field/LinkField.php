<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

use Torr\PrismicApi\CustomType\Exception\InvalidTypeDefinitionException;
use Torr\PrismicApi\CustomType\Helper\FilterFieldsHelper;

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
		?string $select = self::SELECT_ALL,
		?string $placeholder = null,
		?array $customTypes = null,
		?array $tags = null,
	)
	{
		$hasDocumentFilter = !empty($customTypes) || !empty($tags);
		$selectsDocuments = self::SELECT_ALL === $select || self::SELECT_DOCUMENT === $select;

		if ($hasDocumentFilter && !$selectsDocuments)
		{
			throw new InvalidTypeDefinitionException("Can't filter for documents if not actually selecting documents");
		}


		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
			"select" => $select,
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
}
