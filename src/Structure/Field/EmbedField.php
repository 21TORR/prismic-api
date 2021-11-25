<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Data\Value\ImageValue;
use Torr\PrismicApi\Data\Value\VideoValue;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Transform\FieldValueTransformer;

/**
 * @see https://prismic.io/docs/core-concepts/embed
 */
final class EmbedField extends InputField
{
	private const TYPE_KEY = "Embed";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		?string $placeholder = null,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function getValidationConstraints () : array
	{
		return [
			new Assert\NotNull(),
			new Assert\Type("array"),
			new Assert\Collection([
				"fields" => [
					"provider_name" => [
						new Assert\NotNull(),
						new Assert\Type("string"),
						new Assert\Choice(\array_keys(VideoValue::PROVIDER_MAPPING)),
					],
					"type" => [
						new Assert\NotNull(),
						// currently, only videos are supported
						new Assert\IdenticalTo("video"),
					],
					"title" => [
						new Assert\NotNull(),
						new Assert\Type("string"),
					],
					"width" => [
						new Assert\NotNull(),
						new Assert\Type("int"),
						new Assert\Range(min: 1),
					],
					"height" => [
						new Assert\NotNull(),
						new Assert\Type("int"),
						new Assert\Range(min: 1),
					],
					"thumbnail_url" => [
						new Assert\NotNull(),
						new Assert\Type("string"),
					],
					"thumbnail_width" => [
						new Assert\NotNull(),
						new Assert\Type("int"),
						new Assert\Range(min: 1),
					],
					"thumbnail_height" => [
						new Assert\NotNull(),
						new Assert\Type("int"),
						new Assert\Range(min: 1),
					],
					"embed_url" => [
						new Assert\NotNull(),
						new Assert\Type("string"),
					],
				],
				"allowMissingFields" => false,
				"allowExtraFields" => true,
			])
		];
	}

	/**
	 * @inheritDoc
	 */
	public function transformValue (mixed $data, FieldValueTransformer $valueTransformer) : mixed
	{
		dump($data);
		return new VideoValue(
			provider: VideoValue::PROVIDER_MAPPING[$data["provider_name"]],
			url: $data["embed_url"],
			title: $data["title"],
			width: $data["width"],
			height: $data["height"],
			thumbnail: new ImageValue(
				url: $data["thumbnail_url"],
				width: $data["thumbnail_width"],
				height: $data["thumbnail_height"],
			),
		);
	}
}
