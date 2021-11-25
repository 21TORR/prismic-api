<?php declare(strict_types=1);

namespace Torr\PrismicApi\Data;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validation;
use Torr\PrismicApi\Exception\Data\InvalidDataStructureException;

final class Environment
{
	private string $masterRefId;
	private array $languages = [];

	/**
	 *
	 */
	public function __construct (
		private array $data,
	)
	{
		$this->validateDataStructure($this->data, $this->getValidationConstraints());
		$this->masterRefId = $this->findMasterRefId($data["refs"]);

		foreach ($data["languages"] as $language)
		{
			$this->languages[$language["id"]] = $language["name"];
		}
	}


	/**
	 */
	public function getMasterRefId () : string
	{
		return $this->masterRefId;
	}


	/**
	 * @return array<string, string>
	 */
	public function getLanguages () : array
	{
		return $this->languages;
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

		throw new InvalidDataStructureException("Found no master ref", $validatedRefs);
	}


	/**
	 *
	 */
	private function getValidationConstraints () : array
	{
		return [
			new Assert\Collection([
				"fields" => [
					"refs" => [
						new Assert\NotNull(),
						new Assert\Type("array"),
						new Assert\All([
							"constraints" => [
								new Assert\Collection([
									"fields" => [
										"id" => [
											new Assert\NotNull(),
											new Assert\Type("string"),
										],
										"ref" => [
											new Assert\NotNull(),
											new Assert\Type("string"),
										],
										"label" => [
											new Assert\NotNull(),
											new Assert\Type("string"),
										],
									],
									"allowExtraFields" => true,
									"allowMissingFields" => false,
								]),
								new Assert\Collection([
									"fields" => [
										"isMasterRef" => [
											new Assert\NotNull(),
											new Assert\Type("bool"),
										],
									],
									"allowExtraFields" => true,
									"allowMissingFields" => true,
								]),
							],
						]),
					],
					"types" => [
						new Assert\NotNull(),
						new Assert\Type("array"),
						new Assert\Callback(
							function (array $array, ExecutionContextInterface $context) : void
							{
								foreach ($array as $key => $value)
								{
									if (!\is_string($key) || !\is_string($value))
									{
										$context
											->buildViolation("Invalid key/value in environment languages at key '{{ key }}' with value '{{ value }}'")
											->setParameters([
												"{{ key }}" => $key,
												"{{ value }}" => $value,
											])
											->addViolation();
									}
								}
							},
						),
					],
					"languages" => [
						new Assert\NotNull(),
						new Assert\Type("array"),
						new Assert\All([
							"constraints" => [
								new Assert\Collection([
									"fields" => [
										"id" => [
											new Assert\NotNull(),
											new Assert\Type("string"),
										],
										"name" => [
											new Assert\NotNull(),
											new Assert\Type("string"),
										],
									],
									"allowExtraFields" => true,
									"allowMissingFields" => false,
								]),
							],
						]),
					],
				],
				"allowExtraFields" => true,
				"allowMissingFields" => false,
			]),
		];
	}


	/**
	 * Validates the given data according to the constraints.
	 *
	 * @param Constraint[] $constraints
	 * @param string|null  $validationMessage A message that is added to the exception in case the validation fails.
	 */
	private function validateDataStructure (array $data, array $constraints, ?string $validationMessage = null) : void
	{
		// always valid if no constraints given
		if (0 === \count($constraints))
		{
			return;
		}

		$validator = Validation::createValidator();
		$violations = $validator->validate($data, $constraints);

		if (\count($violations) > 0)
		{
			throw new InvalidDataStructureException(
				self::class,
				$data,
				$violations,
				$validationMessage,
			);
		}
	}
	//endregion
}
