<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data;

use Torr\PrismicApi\Exception\Data\InvalidEnvironmentException;

final class PrismicEnvironment
{
	private string $masterRefId;
	private array $languages = [];

	public function __construct (
		private array $refs,
		private array $types,
		array $languages,
	)
	{
		$this->masterRefId = $this->findMasterRefId($this->refs);

		foreach ($languages as $language)
		{
			$this->languages[$language["id"]] = $language["name"];
		}
	}

	//region Sanitize & Validate
	/**
	 */
	private function findMasterRefId (array $validatedRefs) : string
	{
		foreach ($validatedRefs as $ref)
		{
			if ($ref["isMasterRef"] ?? false)
			{
				return $ref["ref"];
			}
		}

		throw new InvalidEnvironmentException("Found no master ref");
	}
	//endregion


	/**
	 */
	public function getMasterRefId () : string
	{
		return $this->masterRefId;
	}
}
