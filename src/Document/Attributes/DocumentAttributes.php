<?php declare(strict_types=1);

namespace Torr\PrismicApi\Document\Attributes;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

final class DocumentAttributes
{
	private \DateTimeImmutable $firstPublicationDate;
	private \DateTimeImmutable $lastPublicationDate;

	/**
	 */
	public function __construct (
		private array $data,
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
	public function getFirstPublicationDate () : \DateTimeImmutable
	{
		return $this->firstPublicationDate;
	}

	/**
	 */
	public function getLastPublicationDate () : \DateTimeImmutable
	{
		return $this->lastPublicationDate;
	}


	/**
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
						new Assert\NotNull(),
						new Assert\Type("string"),
						new Assert\DateTime(\DateTimeInterface::RFC3339),
					],
					"last_publication_date" => [
						new Assert\NotNull(),
						new Assert\Type("string"),
						new Assert\DateTime(\DateTimeInterface::RFC3339),
					],
					"lang" => [
						new Assert\NotNull(),
						new Assert\Type("string"),
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
	private function parseDate (string $date) : \DateTimeImmutable
	{
		$parsed = \DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC3339, $date);
		\assert($parsed instanceof \DateTimeImmutable);
		return $parsed;
	}
}
