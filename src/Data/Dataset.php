<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data;

use Symfony\Component\Validator\Constraint;
use Torr\PrismicApi\Data\Document\Document;

/**
 * A generic dataset container.
 *
 * Is used as basis for every item that comes from Prismic.
 * Documents should extend from {@see Document}, slices should extend {@see Slice}.
 * This class is mainly useful if you want to extract nested data into a separate class for easier usage.
 */
abstract class Dataset
{
	use DataStructureValidationTrait;
	protected array $data;

	/**
	 */
	public function __construct (array $data)
	{
		$this->validateDataStructure($data, $this->getValidationConstraints());
		$this->data = $data;
	}


	/**
	 * Returns the validation constraints for this dataset
	 */
	abstract protected function getValidationConstraints () : ?Constraint;
}
