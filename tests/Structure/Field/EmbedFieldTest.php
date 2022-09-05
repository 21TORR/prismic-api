<?php declare(strict_types=1);

namespace Tests\Torr\PrismicApi\Structure\Field;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Torr\PrismicApi\Exception\Data\DataValidationFailedException;
use Torr\PrismicApi\Structure\Field\EmbedField;
use Torr\PrismicApi\Validation\DataValidator;

final class EmbedFieldTest extends TestCase
{
	public function provideValidData () : iterable
	{
		yield "null" => [null];
		yield "empty array" => [[]];
		yield "YouTube full" => [[
			"provider_name" => "YouTube",
			"type" => "video",
			"title" => "Titel",
			"width" => 123,
			"height" => 234,
			"thumbnail_url" => "https://www.example.org",
			"thumbnail_width" => 234,
			"thumbnail_height" => 123,
			"embed_url" => "https://www.example.org",
		]];
		yield "Vimeo full" => [[
			"provider_name" => "Vimeo",
			"type" => "video",
			"title" => "Titel",
			"width" => 123,
			"height" => 234,
			"thumbnail_url" => "https://www.example.org",
			"thumbnail_width" => 234,
			"thumbnail_height" => 123,
			"embed_url" => "https://www.example.org",
		]];
	}

	/**
	 * @dataProvider provideValidData
	 */
	public function testValidData (mixed $data) : void
	{
		$dataValidator = new DataValidator(Validation::createValidator());
		$field = new EmbedField("Label");
		$field->validateData($dataValidator, [], $data);

		self::assertTrue(true, "Validation succeeded");
	}


	public function provideInvalidData () : iterable
	{
		yield "invalid" => [[
			"test" => "abc",
		]];
		yield "invalid provider" => [[
			"provider_name" => "YouTube2",
			"type" => "video",
			"title" => "Titel",
			"width" => 123,
			"height" => 234,
			"thumbnail_url" => "https://www.example.org",
			"thumbnail_width" => 234,
			"thumbnail_height" => 123,
			"embed_url" => "https://www.example.org",
		]];

		yield [[
			"test" => "abc",
		]];
	}


	/**
	 * @dataProvider provideInvalidData
	 */
	public function testInvalidData (mixed $data) : void
	{
		$this->expectException(DataValidationFailedException::class);

		$dataValidator = new DataValidator(Validation::createValidator());
		$field = new EmbedField("Label");
		$field->validateData($dataValidator, ["test"], $data);
	}
}
