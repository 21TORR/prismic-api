<?php declare(strict_types=1);

namespace Torr\PrismicApi\Definition;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Data\Document;
use Torr\PrismicApi\Data\DocumentAttributes;
use Torr\PrismicApi\Definition\Configuration\DocumentTypeConfiguration;
use Torr\PrismicApi\Editor\EditorTabs;
use Torr\PrismicApi\Exception\Document\InvalidDocumentStructureException;
use Torr\PrismicApi\Validation\DataValidator;

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
	 * Validates the data according to the validation constraints and possibly throws a validation exception.
	 *
	 * @internal
	 */
	public function validateData (DataValidator $validator, array $data) : void
	{
		$constraints = DocumentAttributes::getValidationConstraints();
		$constraints[] = new Assert\Collection([
			"fields" => [
				"data" => [
					new Assert\NotNull(),
					new Assert\Type("array"),
					new Assert\Collection([
						"fields" => [
							new Assert\NotNull(),
							new Assert\Type("array"),
						],
						"allowExtraFields" => true,
						"allowMissingFields" => true,
					]),
				],
			],
			"allowExtraFields" => true,
			"allowMissingFields" => false,
		]);

		// validate itself
		$path = [
			\sprintf("Document<%s>", DataValidator::getBaseClassName(static::class, "Definition")),
		];
		$validator->ensureDataIsValid($path, static::class, $data, $constraints);

		// validate nested data
		foreach ($this->getEditorTabs()->getFields() as $key => $inputField)
		{
			$inputField->validateData(
				$validator,
				[...$path, $key],
				$data["data"][$key] ?? null,
			);
		}
	}

	/**
	 * @phpstan-return T
	 */
	final public function createDocument (array $data, DataValidator $validator) : Document
	{
		$dataClass = $this->getDataClass();

		if (!\is_a($dataClass, Document::class, true))
		{
			throw new InvalidDocumentStructureException(\sprintf(
				"Can't create document of type '%s', as it does not extend Document.",
				$dataClass,
			));
		}

		// validate the data before creating the object
		$this->validateData($validator, $data);

		return new $dataClass($data, $this->getEditorTabs());
	}
}
