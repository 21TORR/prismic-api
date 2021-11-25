<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Part\ImageConstraint;
use Torr\PrismicApi\Structure\Part\Thumbnail;

/**
 * @see https://prismic.io/docs/core-concepts/image
 */
final class ImageField extends InputField
{
	private const TYPE_KEY = "Image";


	/**
	 * @inheritDoc
	 *
	 * @param Thumbnail[] $thumbnails
	 */
	public function __construct (
		string $label,
		?string $placeholder = null,
		?ImageConstraint $imageConstraint = null,
		array $thumbnails = [],
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
			"constraint" => $imageConstraint?->formatTypeDefinition(),
			"thumbnails" => !empty($thumbnails)
				? \array_map(
					static fn (Thumbnail $thumbnail) => $thumbnail->formatTypeDefinition(),
					$thumbnails,
				)
				: null,
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
