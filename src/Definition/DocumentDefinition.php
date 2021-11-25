<?php declare(strict_types=1);

namespace Torr\PrismicApi\Definition;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Data\Document;
use Torr\PrismicApi\Data\DocumentAttributes;
use Torr\PrismicApi\Definition\Configuration\DocumentTypeConfiguration;
use Torr\PrismicApi\Editor\EditorTabs;
use Torr\PrismicApi\Exception\Data\InvalidDataStructureException;
use Torr\PrismicApi\Exception\Document\InvalidDocumentStructureException;

/**
 * @phpstan-template T of Document
 */
abstract class DocumentDefinition
{
	protected ?EditorTabs $editorTabs = null;


	/**
	 * ID of the prismic type
	 */
	abstract public function getTypeId () : string;


	/**
	 * Configures the document type in prismic
	 */
	abstract public function configureType () : DocumentTypeConfiguration;

	/**
	 * Returns the FQCN of the data class for this type
	 *
	 * @phpstan-return class-string<T>
	 */
	abstract public function getDataClass () : string;

	/**
	 *
	 */
	public function getEditorTabs () : EditorTabs
	{
		if (null === $this->editorTabs)
		{
			$this->editorTabs = $this->configureEditorTabs();

			if (empty($this->editorTabs->getFields()))
			{
				throw new InvalidDocumentStructureException(\sprintf(
					"Can't use empty editor tabs in '%s'",
					static::class,
				));
			}
		}

		return $this->editorTabs;
	}

	/**
	 */
	abstract protected function configureEditorTabs () : EditorTabs;


	/**
	 * Returns the validation constraints to validate the given data from prismic,
	 * according to field definitions.
	 *
	 * @internal
	 *
	 * @return Constraint[]
	 */
	private function getValidationConstraints () : array
	{
		$constraints = DocumentAttributes::getValidationConstraints();

		$collectionFields = [];

		foreach ($this->getEditorTabs()->getFields() as $key => $inputField)
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
	 * @phpstan-return T
	 */
	final public function createDocument (array $data, ValidatorInterface $validator) : Document
	{
		$dataClass = $this->getDataClass();

		if (!\is_a($dataClass, Document::class, true))
		{
			throw new InvalidDocumentStructureException(\sprintf(
				"Can't create document of type '%s', as it does not extend Document.",
				$dataClass,
			));
		}

		$violations = $validator->validate($data, $this->getValidationConstraints());

		if (0 < \count($violations))
		{
			throw new InvalidDataStructureException(
				static::class,
				$data,
				$violations,
			);
		}

		return new $dataClass($data, $this->getEditorTabs());
	}
}
