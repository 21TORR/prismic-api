<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data\Document;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Data\Dataset;
use Torr\PrismicApi\Data\DataStructureValidationTrait;

/**
 * A top level base class for representing documents
 */
abstract class Document extends Dataset
{
	use DataStructureValidationTrait;
	protected DocumentAttributes $attributes;

	/**
	 */
	public function __construct (array $data)
	{
		$this->validateDataStructure(
			$data,
			new Assert\Collection([
				"fields" => [
					"data" => [
						new Assert\NotNull(),
						new Assert\Type("array"),
					],
				],
				"allowExtraFields" => true,
				"allowMissingFields" => false,
			]),
		);

		parent::__construct($data["data"]);
		$this->attributes = new DocumentAttributes($data);
	}

	/**
	 */
	public function getAttributes () : DocumentAttributes
	{
		return $this->attributes;
	}
}
