<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

final class DocumentAttributes
{
	private ?\DateTimeImmutable $firstPublicationDate;
	private ?\DateTimeImmutable $lastPublicationDate;

	/**
	 */
	public function __construct (
		private readonly array $data,
	)
	{
		$this->firstPublicationDate = $this->parseDate($data["first_publication_date"]);
		$this->lastPublicationDate = $this->parseDate($data["last_publication_date"]);
	}


	/**
	 * Returns the ID of the document
	 */
	public function getId () : string
	{
		return $this->data["id"];
	}


	/**
	 * Returns the UID of the document.
	 * If set, it is guaranteed to be unique for this locale + document type.
	 */
	public function getUid () : ?string
	{
		return $this->data["uid"];
	}


	/**
	 * Returns the document type
	 */
	public function getType () : string
	{
		return $this->data["type"];
	}


	/**
	 * Returns the language code of this document
	 */
	public function getLanguage () : string
	{
		return $this->data["lang"];
	}


	/**
	 * Returns the internal document's tags.
	 *
	 * @return string[]
	 */
	public function getTags () : array
	{
		return $this->data["tags"];
	}


	/**
	 */
	public function getFirstPublicationDate () : ?\DateTimeImmutable
	{
		return $this->firstPublicationDate;
	}

	/**
	 */
	public function getLastPublicationDate () : ?\DateTimeImmutable
	{
		return $this->lastPublicationDate;
	}

	/**
	 * Returns the slug of the element
	 */
	public function getSlug () : string|null
	{
		return $this->getAllSlugs()[0] ?? null;
	}

	/**
	 * Returns the current slug + all previous slugs of this item
	 *
	 * @return string[]
	 */
	public function getAllSlugs () : array
	{
		$slugs = \array_filter([
			$this->getUid(),
			// the slugs lists all slugs that element had
			...$this->data["slugs"],
		]);

		// return a 0-based array
		return \array_values(
			\array_unique($slugs),
		);
	}

	/**
	 * Returns the alternative languages of this document.
	 * Key is the locale, value is the page id.
	 *
	 * @return array<string, string>
	 */
	public function getAlternateLanguageIds () : array
	{
		$result = [];

		foreach ($this->data["alternate_languages"] as $alternateLanguage)
		{
			$result[$alternateLanguage["lang"]] = $alternateLanguage["id"];
		}

		return $result;
	}


	/**
	 * @internal
	 *
	 * @return Constraint[]
	 */
	public static function getValidationConstraints () : array
	{
		return [
			new Assert\Collection([
				"fields" => [
					"id" => [
						new Assert\NotNull(),
						new Assert\Type("string"),
					],
					"uid" => [
						new Assert\Type("string"),
					],
					"type" => [
						new Assert\NotNull(),
						new Assert\Type("string"),
					],
					"tags" => [
						new Assert\NotNull(),
						new Assert\Type("array"),
						new Assert\All([
							"constraints" => [
								new Assert\NotNull(),
								new Assert\Type("string"),
							],
						]),
					],
					"first_publication_date" => [
						new Assert\Type("string"),
						new Assert\DateTime(\DateTimeInterface::RFC3339),
					],
					"last_publication_date" => [
						new Assert\Type("string"),
						new Assert\DateTime(\DateTimeInterface::RFC3339),
					],
					"lang" => [
						new Assert\NotNull(),
						new Assert\Type("string"),
					],
					"alternate_languages" => [
						new Assert\NotNull(),
						new Assert\Type("array"),
						new Assert\All([
							"constraints" => [
								new Assert\NotNull(),
								new Assert\Collection(
									fields: [
										"id" => [
											new Assert\NotNull(),
											new Assert\Type("string"),
										],
										"lang" => [
											new Assert\NotNull(),
											new Assert\Type("string"),
										],
									],
									allowExtraFields: true,
									allowMissingFields: false,
								),
							],
						]),
					],
				],
				"allowMissingFields" => false,
				"allowExtraFields" => true,
			]),
		];
	}


	/**
	 * Parses the given date
	 */
	private function parseDate (?string $date) : ?\DateTimeImmutable
	{
		if (null === $date)
		{
			return null;
		}

		$parsed = \DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC3339, $date);
		\assert($parsed instanceof \DateTimeImmutable);
		return $parsed;
	}
}
