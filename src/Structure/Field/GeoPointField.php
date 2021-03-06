<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Transform\DataTransformer;
use Torr\PrismicApi\Validation\DataValidator;
use Torr\PrismicApi\Visitor\DataVisitorInterface;

/**
 * @see https://prismic.io/docs/core-concepts/geopoint
 */
final class GeoPointField extends InputField
{
	private const TYPE_KEY = "GeoPoint";


	/**
	 * @inheritDoc
	 */
	public function __construct (
		string $label,
		private readonly bool $required = false,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function validateData (DataValidator $validator, array $path, mixed $data) : void
	{
		$this->ensureDataIsValid($validator, $path, $data, [
			new Assert\Type("array"),
			new Assert\Collection([
				"fields" => [
					"latitude" => [
						new Assert\NotNull(),
						new Assert\Type("float"),
					],
					"longitude" => [
						new Assert\NotNull(),
						new Assert\Type("float"),
					],
				],
				"allowExtraFields" => true,
				"allowMissingFields" => false,
			]),
			$this->required ? new Assert\NotNull() : null,
		]);
	}

	/**
	 * @inheritDoc
	 *
	 * @template T
	 *
	 * @param T $data
	 *
	 * @return T
	 */
	public function transformValue (
		mixed $data,
		DataTransformer $dataTransformer,
		?DataVisitorInterface $dataVisitor = null,
	) : mixed
	{
		$dataVisitor?->onDataVisit($this, $data);

		return $data;
	}
}
