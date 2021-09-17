<?php
declare(strict_types=1);

namespace Torr\PrismicApi\DataFactory;

use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
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
			$this->logger->error("Invalid prismic environment data", [
				"error" => (string) $violations,
				"violations" => $violations,
			]);
			throw new InvalidEnvironmentException((string) $violations);
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
									"isMasterRef" => [
										new Assert\NotNull(),
										new Assert\Type("bool"),
									],
								],
								"allowExtraFields" => true,
								"allowMissingFields" => false,
							]),
						]
					]),
				],
				"types" => [
					new Assert\NotNull(),
					new Assert\Type("array"),
					new Assert\Callback(
						function (array $array, ExecutionContextInterface $context)
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
						}
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
						]
					]),
				],
			],
			"allowExtraFields" => true,
			"allowMissingFields" => false,
		]);
	}
}
