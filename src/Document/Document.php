<?php declare(strict_types=1);

namespace Torr\PrismicApi\Document;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Document\Attributes\DocumentAttributes;
use Torr\PrismicApi\Document\Configuration\DocumentTypeConfiguration;
use Torr\PrismicApi\Document\Editor\EditorTabs;
use Torr\PrismicApi\Exception\Data\InvalidDataStructureException;
use Torr\PrismicApi\Factory\DocumentFactory;

/**
 * Base class for any document in Prismic
 */
abstract class Document implements DocumentInterface
{
	protected array $data;
	protected DocumentAttributes $attributes;
	protected static ?EditorTabs $editorTabs = null;

	/**
	 * You should never create an instance manually, always use {@see DocumentFactory} instead.
	 *
	 * @internal
	 */
	final public function __construct (array $data = [])
	{
		$this->data = $data["data"];
		$this->attributes = new DocumentAttributes($data);
	}

	/**
	 */
	public function getAttributes () : DocumentAttributes
	{
		return $this->attributes;
	}


	/**
	 * ID of the prismic type
	 */
	abstract public static function getDocumentTypeId () : string;

	/**
	 * Configures the document type in prismic
	 */
	abstract public static function configureType () : DocumentTypeConfiguration;


	/**
	 * @internal
	 */
	public static function getEditorTabs () : EditorTabs
	{
		if (null === static::$editorTabs)
		{
			static::$editorTabs = static::configureEditorTabs();
		}

		return static::$editorTabs;
	}

	/**
	 */
	abstract protected static function configureEditorTabs () : EditorTabs;


	/**
	 * Returns the validation constraints to validate the given data from prismic,
	 * according to field definitions.
	 *
	 * @internal
	 *
	 * @return Constraint[]
	 */
	protected static function getValidationConstraints () : array
	{
		$constraints = DocumentAttributes::getValidationConstraints();

		$collectionFields = [];

		foreach (static::getEditorTabs()->getFields() as $key => $inputField)
		{
			$collectionFields[$key] = $inputField->getValidationConstraints();
		}

		$constraints[] = new Assert\Collection([
			"fields" => [
				"data" => [
					new Assert\NotNull(),
					new Assert\Type("array"),
					new Assert\Collection([
						"fields" => $collectionFields,
						"allowExtraFields" => true,
						"allowMissingFields" => true,
					]),
				],
			],
			"allowExtraFields" => true,
			"allowMissingFields" => false,
		]);

		return $constraints;
	}

	/**
	 */
	public static function create (array $data, ValidatorInterface $validator) : static
	{
		$violations = $validator->validate($data, self::getValidationConstraints());

		if (0 < \count($violations))
		{
			throw new InvalidDataStructureException(
				static::class,
				$data,
				$violations,
			);
		}

		return new static($data);
	}
}
