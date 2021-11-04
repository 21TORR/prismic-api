<?php declare(strict_types=1);

namespace Torr\PrismicApi\CustomType\Data\Field;

use Torr\PrismicApi\CustomType\Data\Part\ImageConstraint;
use Torr\PrismicApi\CustomType\Data\Part\Thumbnail;

/**
 * @link https://prismic.io/docs/core-concepts/image
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
		parent::__construct(self::TYPE_KEY, $this->filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
			"constraint" => $imageConstraint?->toArray(),
			"thumbnails" => !empty($thumbnails)
				? \array_map(
					static fn (Thumbnail $thumbnail) => $thumbnail->toArray(),
					$thumbnails
				)
				: null,
		]));
	}
}
