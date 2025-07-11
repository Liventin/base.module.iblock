<?php

namespace Base\Module\Src\Iblock;

use Base\Module\Exception\ModuleException;
use Base\Module\Service\Iblock\IblockItem as IIblockItem;
use Bitrix\Iblock\Iblock;
use Bitrix\Main\ORM\Query\Query;
use Exception;

class IblockItem implements IIblockItem
{
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
}