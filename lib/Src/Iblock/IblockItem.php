<?php

namespace Base\Module\Src\Iblock;

use Base\Module\Exception\ModuleException;
use Base\Module\Service\Iblock\IblockItem as IIblockItem;
use Bitrix\Iblock\Iblock;
use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\SystemException;
use Exception;

class IblockItem implements IIblockItem
{
    private ?array $cacheEnumProps = null;

    public function __construct(
        readonly int $id,
        readonly string $apiCode,
        readonly Iblock $iblock
    ) {
    }

    /**
     * @return Query
     * @throws ModuleException
     */
    public function getQuery(): Query
    {
        try {
            return $this->iblock->getEntityDataClass()::query();
        } catch (Exception $e) {
            throw new ModuleException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $enumPropCode
     * @return array
     * @throws ModuleException
     */
    public function getEnumPropValues(string $enumPropCode): array
    {
        try {
            $this->prepareEnumsPropsValues();
        } catch (Exception $e) {
            throw new ModuleException($e->getMessage(), $e->getCode());
        }

        return $this->cacheEnumProps[$enumPropCode];
    }

    /**
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function prepareEnumsPropsValues(): void
    {
        if ($this->cacheEnumProps !== null) {
            return;
        }

        /** @var Query $query */
        $query = PropertyEnumerationTable::query();
        $enumFields = $query
            ->addSelect('ID')
            ->addSelect('XML_ID')
            ->addSelect('VALUE')
            ->addSelect('PROPERTY.CODE', 'PROP_CODE')
            ->where('PROPERTY.IBLOCK_ID', $this->id)
            ->where('PROPERTY.PROPERTY_TYPE', PropertyTable::TYPE_LIST)
            ->setCacheTtl('86400')
            ->cacheJoins(true)
            ->fetchAll();

        foreach ($enumFields as $field) {
            $this->cacheEnumProps[$field['PROP_CODE']][] = [
                'ID' => $field['ID'],
                'XML_ID' => $field['XML_ID'],
                'VALUE' => $field['VALUE'],
            ];
        }
    }
}