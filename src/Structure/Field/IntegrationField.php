<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Transform\DataTransformer;
use Torr\PrismicApi\Validation\DataValidator;
use Torr\PrismicApi\Visitor\DataVisitorInterface;

/**
 * @see https://prismic.io/docs/core-concepts/integration-fields
 */
final class IntegrationField extends InputField
{
	private const TYPE_KEY = "IntegrationFields";


	/**
	 * @inheritDoc
	 */
	public function __construct(
		string $label,
		string $catalog,
		?string $placeholder = null,
		?string $catalogRepository = null,
	)
	{
		$catalogRepository = $catalogRepository ?? $_ENV['PRISMIC_REPOSITORY'];

		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"catalog" => "{$catalogRepository}--{$catalog}",
			"placeholder" => $placeholder,
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function validateData (
		DataValidator $validator,
		array $path,
		mixed $data,
	) : void
	{
		// @todo add validation
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
