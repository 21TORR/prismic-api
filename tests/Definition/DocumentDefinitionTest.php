<?php declare(strict_types=1);

namespace Tests\Torr\PrismicApi\Definition;

use PHPUnit\Framework\TestCase;
use Tests\Torr\PrismicApi\Fixtures\DocumentDataTrait;
use Torr\PrismicApi\Data\Document;
use Torr\PrismicApi\Definition\DocumentDefinition;
use Torr\PrismicApi\Structure\Field\TextField;
use Torr\PrismicApi\Definition\Configuration\DocumentTypeConfiguration;
use Torr\PrismicApi\Editor\EditorTabs;
use Torr\PrismicApi\Exception\Document\InvalidDocumentStructureException;

final class DocumentDefinitionTest extends TestCase
{
	use DocumentDataTrait;

	/**
	 */
	public function testEditorTabsAreCached () : void
	{
		$type = new class extends DocumentDefinition
		{
			/**
			 * @inheritDoc
			 */
			public function getTypeId () : string
			{
				return "test";
			}

			/**
			 * @inheritDoc
			 */
			public function getDataClass () : string
			{
				return "test";
			}

			/**
			 * @inheritDoc
			 */
			public function configureType () : DocumentTypeConfiguration
			{
				return new DocumentTypeConfiguration("Test");
			}

			/**
			 * @inheritDoc
			 */
			protected function configureEditorTabs () : EditorTabs
			{
				return (new EditorTabs())
					->addTab("Test", [
						"test" => new TextField("Test"),
					]);
			}
		};

		$tabs = $type->getEditorTabs();
		$tabs2 = $type->getEditorTabs();

		self::assertSame($tabs, $tabs2);
	}

	/**
	 */
	public function testEmptyEditorTabsAreInvalid () : void
	{
		$type = new class extends DocumentDefinition
		{
			/**
			 * @inheritDoc
			 */
			public function getTypeId () : string
			{
				return "test";
			}

			/**
			 * @inheritDoc
			 */
			public function getDataClass () : string
			{
				return "test";
			}

			/**
			 * @inheritDoc
			 */
			public function configureType () : DocumentTypeConfiguration
			{
				return new DocumentTypeConfiguration("Test");
			}

			/**
			 * @inheritDoc
			 */
			protected function configureEditorTabs () : EditorTabs
			{
				return (new EditorTabs());
			}
		};

		$this->expectException(InvalidDocumentStructureException::class);
		$type->getEditorTabs();
	}
}
