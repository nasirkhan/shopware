<?php

namespace Shopware\Unit\Loader;

use Doctrine\DBAL\Connection;
use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Struct\SortArrayByKeysTrait;
use Shopware\Unit\Factory\UnitBasicFactory;
use Shopware\Unit\Struct\UnitBasicCollection;
use Shopware\Unit\Struct\UnitBasicStruct;

class UnitBasicLoader
{
    use SortArrayByKeysTrait;

    /**
     * @var UnitBasicFactory
     */
    private $factory;

    public function __construct(
        UnitBasicFactory $factory
    ) {
        $this->factory = $factory;
    }

    public function load(array $uuids, TranslationContext $context): UnitBasicCollection
    {
        if (empty($uuids)) {
            return new UnitBasicCollection();
        }

        $unitsCollection = $this->read($uuids, $context);

        return $unitsCollection;
    }

    private function read(array $uuids, TranslationContext $context): UnitBasicCollection
    {
        $query = $this->factory->createQuery($context);

        $query->andWhere('unit.uuid IN (:ids)');
        $query->setParameter(':ids', $uuids, Connection::PARAM_STR_ARRAY);

        $rows = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
        $structs = [];
        foreach ($rows as $row) {
            $struct = $this->factory->hydrate($row, new UnitBasicStruct(), $query->getSelection(), $context);
            $structs[$struct->getUuid()] = $struct;
        }

        return new UnitBasicCollection(
            $this->sortIndexedArrayByKeys($uuids, $structs)
        );
    }
}
