<?php declare(strict_types=1);

namespace Torr\PrismicApi\Structure\Field;

use Symfony\Component\Validator\Constraints as Assert;
use Torr\PrismicApi\Structure\Helper\FilterFieldsHelper;
use Torr\PrismicApi\Structure\Helper\KeyedMapHelper;
use Torr\PrismicApi\Transform\DataTransformer;
use Torr\PrismicApi\Validation\DataValidator;
use Torr\PrismicApi\Visitor\DataVisitorInterface;

/**
 * @see https://prismic.io/docs/core-concepts/group
 */
final class GroupField extends InputField
{
	private const TYPE_KEY = "Group";


	/**
	 * @inheritDoc
	 *
	 * @param array<string, InputField> $fields
	 */
	public function __construct (
		string $label,
		private readonly array $fields,
		?bool $repeat = false,
		private readonly bool $required = false,
	)
	{
		parent::__construct(self::TYPE_KEY, FilterFieldsHelper::filterOptionalFields([
			"label" => $label,
			"repeat" => $repeat,
			"fields" => KeyedMapHelper::transformKeyedListOfTypes($this->fields),
		]));
	}

	/**
	 * @inheritDoc
	 */
	public function validateData (DataValidator $validator, array $path, mixed $data) : void
	{
		// validate field itself
		$this->ensureDataIsValid($validator, $path, $data, [
			new Assert\Type("array"),
			$this->required ? new Assert\NotNull() : null,
			$this->required ? new Assert\Count(min: 1) : null,
		]);

		// If the document already exists and the field was added later, the data does not exist yet.
		// The data is only there or in the structure when the document is saved again in Prismic, before that it is null or the previous structure.
		if (!\is_array($data))
		{
			return;
		}

		// validate nested fields
		foreach ($data as $index => $nestedData)
		{
			foreach ($this->fields as $key => $field)
			{
				$field->validateData(
					$validator,
					[...$path, $index, $key],
					$nestedData[$key] ?? null,
				);
			}
		}
	}

	/**
	 * @inheritDoc
	 *
	 * @template T of array
	 *
	 * @param T $data
	 *
	 * @return T
	 */
	public function transformValue (
		mixed $data,
		DataTransformer $dataTransformer,
		?DataVisitorInterface $dataVisitor = null,
	) : array
	{
		$dataVisitor?->onDataVisit($this, $data);

		$result = [];

		foreach ($data as $entry)
		{
			$transformed = [];

			foreach ($this->fields as $key => $field)
			{
				$transformed[$key] = $field->transformValue(
					$entry[$key] ?? null,
					$dataTransformer,
					$dataVisitor,
				);
			}

			$result[] = $transformed;
		}

		return $result;
	}
}
