<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Part\ImageConstraint;
use Torr\PrismicApi\Structure\Validation\ValueValidationTrait;
use Torr\PrismicApi\Transform\FieldValueTransformer;

/**
 * @see https://prismic.io/docs/core-concepts/rich-text-title
 */
class RichTextField extends InputField
{
	use ValueValidationTrait;
	private const TYPE_KEY = "StructuredText";
	private const PARAGRAPH = "paragraph";
	public const HEADINGS = [
		"heading1",
		"heading2",
		"heading3",
		"heading4",
		"heading5",
		"heading6",
	];
	public const INLINE_STYLE = [
		"strong",
		"em",
		"hyperlink",
	];
	public const PARAGRAPH_STYLE = [
		self::PARAGRAPH,
		"preformatted",
		"list-item",
		"o-list-item",
		"image",
		"embed",
	];
	public const RTL = "rtl";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		private readonly ?array $styles = null,
		private readonly bool $allowsMultipleLines = true,
		bool $allowTargetBlankForLinks = true,
		?string $placeholder = null,
		?ImageConstraint $imageConstraint = null,
		private readonly bool $required = false,
	)
	{
		$stylesKey = $this->allowsMultipleLines ? "multi" : "single";

		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"placeholder" => $placeholder,
			$stylesKey => \implode(", ", $this->getAllowedStyles()),
			"allowTargetBlank" => $allowTargetBlankForLinks,
			"imageConstraint" => $imageConstraint?->formatTypeDefinition(),
		]));
	}

	/**
	 * Returns all allowed styles for the rich text editor
	 */
	private function getAllowedStyles () : array
	{
		return $this->styles ?? [
			...self::HEADINGS,
			...self::INLINE_STYLE,
			...self::PARAGRAPH_STYLE,
		];
	}

	/**
	 * Returns all allowed paragraph-level styles
	 */
	private function getAllowedParagraphLevelStyles () : array
	{
		$styles = \array_filter(
			$this->getAllowedStyles(),
			static fn (string $style) => \in_array($style, self::HEADINGS, true) || \in_array($style, self::PARAGRAPH_STYLE, true),
		);

		return !empty($styles)
			? $styles
			: [self::PARAGRAPH];
	}

	/**
	 * @inheritDoc
	 */
	public function validateData (ValidatorInterface $validator, mixed $data) : void
	{
		$constraints = [
			new Assert\Type("array"),
			new Assert\All([
				"constraints" => [
					new Assert\Type("array"),
					new Assert\Collection([
						"fields" => [
							"type" => [
								new Assert\NotNull(),
								new Assert\Type("string"),
								new Assert\Choice(
									choices: $this->getAllowedParagraphLevelStyles(),
								),
							],
						],
						"allowExtraFields" => true,
						"allowMissingFields" => false,
					]),
				],
			]),
		];

		if ($this->required)
		{
			$constraints[] = new Assert\NotNull();
			$constraints[] = new Assert\Count(min: 1);
		}

		if (!$this->allowsMultipleLines)
		{
			$constraints[] = new Assert\Count(max: 1);
		}

		$this->ensureDataIsValid($validator, $data, $constraints);
	}


	/**
	 * @inheritDoc
	 */
	public function transformValue (mixed $data, FieldValueTransformer $valueTransformer) : mixed
	{
		return \is_array($data)
			? $valueTransformer->transformRichText($data)
			: null;
	}
}
