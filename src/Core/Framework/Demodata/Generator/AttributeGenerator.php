<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Demodata\Generator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Faker\Generator;
use Shopware\Core\Framework\Attribute\Aggregate\AttributeSet\AttributeSetDefinition;
use Shopware\Core\Framework\Attribute\AttributeTypes;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Demodata\DemodataContext;
use Shopware\Core\Framework\Demodata\DemodataGeneratorInterface;
use Shopware\Core\Framework\Uuid\Uuid;

class AttributeGenerator implements DemodataGeneratorInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    private $attributeSetRepository;

    /**
     * @var Connection
     */
    private $connection;

    private $attributeSets = [];

    /**
     * @var DefinitionRegistry
     */
    private $definitionRegistry;

    public function __construct(EntityRepositoryInterface $attributeSetRepository, Connection $connection, DefinitionRegistry $definitionRegistry)
    {
        $this->attributeSetRepository = $attributeSetRepository;
        $this->connection = $connection;
        $this->definitionRegistry = $definitionRegistry;
    }

    public function getDefinition(): string
    {
        return AttributeSetDefinition::class;
    }

    public function getRandomSet(): ?array
    {
        return $this->attributeSets ? $this->attributeSets[array_rand($this->attributeSets)] : null;
    }

    public function generate(int $numberOfItems, DemodataContext $context, array $options = []): void
    {
        $console = $context->getConsole();
        $console->comment('Generate attribute sets: ' . $numberOfItems);
        $console->progressStart($numberOfItems);

        for ($i = 0; $i < $numberOfItems; ++$i) {
            $this->generateAttributeSet($options, $context);

            $console->progressAdvance(1);
        }
        $console->progressFinish();

        $relations = $options['relations'];
        $sum = array_sum($relations);
        if ($sum <= 0) {
            return;
        }

        $console->comment('Set attributes for entities: ' . $sum);
        $console->progressStart($sum);
        foreach ($relations as $relation => $count) {
            if (!$count || $count < 1) {
                continue;
            }

            $console->comment('\nSet attributes for ' . $count . ' ' . $relation . ' entities');

            $rndSet = $this->getRandomSet();
            $this->generateAttributeValues($relation, $count, $rndSet['attributes'], $context);

            $console->progressAdvance($count);
        }
        $console->progressFinish();
    }

    private function randomAttribute($prefix, DemodataContext $context): array
    {
        $types = [
            AttributeTypes::INT,
            AttributeTypes::FLOAT,
            AttributeTypes::DATETIME,
            AttributeTypes::BOOL,
            AttributeTypes::TEXT,
        ];

        $name = $context->getFaker()->unique()->words(3, true);
        $type = $types[array_rand($types)];

        switch ($type) {
            case AttributeTypes::INT:
                $config = [
                    'componentName' => 'sw-field',
                    'type' => 'number',
                    'numberType' => 'int',
                    'attributeType' => 'number',
                    'label' => [
                        'en-GB' => $name,
                    ],
                    'placeholder' => [
                        'en-GB' => 'Type a number...',
                    ],
                    'attributePosition' => 1,
                ];
                break;
            case AttributeTypes::FLOAT:
                $config = [
                    'componentName' => 'sw-field',
                    'type' => 'number',
                    'numberType' => 'float',
                    'attributeType' => 'number',
                    'label' => [
                        'en-GB' => $name,
                    ],
                    'placeholder' => [
                        'en-GB' => 'Type a floating number...',
                    ],
                    'attributePosition' => 1,
                ];
                break;
            case AttributeTypes::DATETIME:
                $config = [
                    'componentName' => 'sw-field',
                    'type' => 'date',
                    'dateType' => 'datetime',
                    'attributeType' => 'date',
                    'label' => [
                        'en-GB' => $name,
                    ],
                    'attributePosition' => 1,
                ];
                break;
            case AttributeTypes::BOOL:
                $config = [
                    'componentName' => 'sw-field',
                    'type' => 'checkbox',
                    'attributeType' => 'checkbox',
                    'label' => [
                        'en-GB' => $name,
                    ],
                    'attributePosition' => 1,
                ];
                break;
            default:
                $config = [
                    'componentName' => 'sw-field',
                    'type' => 'text',
                    'attributeType' => 'text',
                    'label' => [
                        'en-GB' => $name,
                    ],
                    'placeholder' => [
                        'en-GB' => 'Type a text...',
                    ],
                    'attributePosition' => 1,
                ];
                break;
        }

        return [
            'id' => Uuid::randomHex(),
            'name' => strtolower($prefix) . '_' . str_replace(' ', '_', $name),
            'type' => $type,
            'config' => $config,
        ];
    }

    private function generateAttributeSet(array $options, DemodataContext $context): void
    {
        $relationNames = array_keys($options['relations']);
        $relations = array_map(function ($rel) {
            return ['id' => Uuid::randomHex(), 'entityName' => $rel];
        }, $relationNames);

        $attributeCount = random_int(1, 5);
        $attributes = [];

        $setName = $context->getFaker()->unique()->category;
        $prefix = 'custom_';

        for ($j = 0; $j < $attributeCount; ++$j) {
            $attributes[] = $this->randomAttribute($prefix . $setName, $context);
        }

        $set = [
            'id' => Uuid::randomHex(),
            'name' => $prefix . $setName,
            'config' => [
                'label' => [
                    'en-GB' => $setName,
                ],
            ],
            'relations' => $relations,
            'attributes' => $attributes,
        ];
        $this->attributeSets[$set['id']] = $set;
        $this->attributeSetRepository->upsert([$set], $context->getContext());
    }

    private function generateAttributeValues($entityName, $count, array $attributes, DemodataContext $context): void
    {
        $repo = $this->definitionRegistry->getRepository($entityName);

        $ids = $this->connection->executeQuery(
            sprintf('SELECT LOWER(HEX(id)) FROM `%s` ORDER BY rand() LIMIT %s', $entityName, $count)
        )->fetchAll(FetchMode::COLUMN);

        $chunkSize = 50;
        foreach (array_chunk($ids, $chunkSize) as $chunk) {
            $updates = [];
            $attributeValues = [];
            foreach ($attributes as $attribute) {
                $attributeValues[$attribute['name']] = $this->randomAttributeValue($attribute['type'], $context->getFaker());
            }

            foreach ($chunk as $id) {
                $updates[] = ['id' => $id, 'attributes' => $attributeValues];
            }
            $repo->update($updates, $context->getContext());
        }
    }

    private function randomAttributeValue(string $type, Generator $faker)
    {
        switch ($type) {
            case AttributeTypes::BOOL:
                return (bool) random_int(0, 1);

            case AttributeTypes::FLOAT:
                return $faker->randomFloat();

            case AttributeTypes::INT:
                return random_int(-1000000, 1000000);

            case AttributeTypes::DATETIME:
                return $faker->dateTime;

            case AttributeTypes::TEXT:
            default:
                return $faker->text();
        }
    }
}
