<?php declare(strict_types=1);

namespace Tests\Torr\PrismicApi\Editor;

use PHPUnit\Framework\TestCase;
use Torr\PrismicApi\Structure\Field\TextField;
use Torr\PrismicApi\Editor\EditorTabs;
use Torr\PrismicApi\Exception\Document\InvalidDocumentStructureException;

final class EditorTabsTest extends TestCase
{
	/**
	 */
	public function testEmptyTabInvalid () : void
	{
		$this->expectException(InvalidDocumentStructureException::class);

		(new EditorTabs())
			->addTab("Test", []);
	}

	/**
	 */
	public function testDuplicateLabelInvalid () : void
	{
		$this->expectException(InvalidDocumentStructureException::class);

		(new EditorTabs())
			->addTab("Test", [
				"test" => new TextField("test"),
			])
			->addTab("Test", [
				"test2" => new TextField("test"),
			]);
	}

	/**
	 */
	public function testDuplicateFieldInvalid () : void
	{
		$this->expectException(InvalidDocumentStructureException::class);

		(new EditorTabs())
			->addTab("Test", [
				"test" => new TextField("test"),
			])
			->addTab("Test 2", [
				"test" => new TextField("test"),
			]);
	}
}
