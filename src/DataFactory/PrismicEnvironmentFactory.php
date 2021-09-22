<?php declare(strict_types=1);

namespace Torr\PrismicApi\DataFactory;

use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Torr\PrismicApi\Data\PrismicEnvironment;
use Torr\PrismicApi\Exception\Data\InvalidEnvironmentException;

final class PrismicEnvironmentFactory
{
	/**
	 */
	public function __construct (
		private ValidatorInterface $validator,
		private LoggerInterface $logger,
	)
	{
	}


	/**
	 */
	public function createEnvironment (array $data) : PrismicEnvironment
	{
		$violations = $this->validator->validate($data, $this->getEnvironmentValidationConstraints());

		if (0 !== \count($violations))
		{
			$errorMessage = $violations instanceof ConstraintViolationList
				? (string) $violations
				: "n/a";

			$this->logger->error("Invalid prismic environment data", [
				"error" => $errorMessage,
				"violations" => $violations,
			]);
			throw new InvalidEnvironmentException($errorMessage);
		}

		return new PrismicEnvironment($data["refs"], $data["types"], $data["languages"]);
	}


	/**
	 */
	private function getEnvironmentValidationConstraints () : Constraint
	{
		return new Assert\Collection([
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
							new Assert\Callback(
								static function (array $data, ExecutionContextInterface $executionContext) : void
								{
									if (\array_key_exists("isMasterRef", $data) && !\is_bool($data["isMasterRef"]))
									{
										$executionContext
											->buildViolation("isMasterRef must be a bool, if set.")
											->addViolation();
									}
								},
							),
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
		]);
	}
}
