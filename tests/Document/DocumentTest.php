<?php declare(strict_types=1);

namespace Tests\Torr\PrismicApi\Document;

use PHPUnit\Framework\TestCase;
use Tests\Torr\PrismicApi\Fixtures\DocumentDataTrait;
use Torr\PrismicApi\Structure\Field\TextField;
use Torr\PrismicApi\Definition\Configuration\DocumentTypeConfiguration;
use Torr\PrismicApi\Document\Document;
use Torr\PrismicApi\Editor\EditorTabs;
use Torr\PrismicApi\Exception\Document\InvalidDocumentStructureException;

final class DocumentTest extends TestCase
{
	use DocumentDataTrait;

	/**
	 */
	public function testEditorTabsAreCached () : void
	{
		$type = new class ($this->getExampleDocumentData()) extends Document
		{
			/**
			 * @inheritDoc
			 */
			public static function getDocumentTypeId () : string
			{
				return "test";
			}

			/**
			 * @inheritDoc
			 */
			public static function configureType () : DocumentTypeConfiguration
			{
				return new DocumentTypeConfiguration("Test");
			}

			/**
			 * @inheritDoc
			 */
			protected static function configureEditorTabs () : EditorTabs
			{
				return (new EditorTabs())
					->addTab("Test", [
						"test" => new TextField("Test"),
					]);
			}
		};

		$tabs = $type::getEditorTabs();
		$tabs2 = $type::getEditorTabs();

		self::assertSame($tabs, $tabs2);
	}

	/**
	 */
	public function testEmptyEditorTabsAreInvalid () : void
	{
		$type = new class ($this->getExampleDocumentData()) extends Document
		{
			/**
			 * @inheritDoc
			 */
			public static function getDocumentTypeId () : string
			{
				return "test";
			}

			/**
			 * @inheritDoc
			 */
			public static function configureType () : DocumentTypeConfiguration
			{
				return new DocumentTypeConfiguration("Test");
			}

			/**
			 * @inheritDoc
			 */
			protected static function configureEditorTabs () : EditorTabs
			{
				return (new EditorTabs());
			}
		};

		$this->expectException(InvalidDocumentStructureException::class);
		$type::getEditorTabs();
	}
}
