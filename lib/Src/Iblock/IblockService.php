<?php

/** @noinspection PhpUnused */

namespace Base\Module\Src\Iblock;

use Base\Module\Exception\ModuleException;
use Base\Module\Service\Iblock\IblockService as IIblockService;
use Base\Module\Service\LazyService;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Throwable;

#[LazyService(serviceCode: IIblockService::SERVICE_CODE, constructorParams: [])]
class IblockService implements IIblockService
{
    private array $cacheIBlocksByApiCode = [];
    private array $cacheIBlocksById = [];

    /**
     * @throws ModuleException
     */
    public function getByApiCode(string $apiCode): object
    {
        if (array_key_exists( $apiCode, $this->cacheIBlocksByApiCode)) {
            return $this->cacheIBlocksByApiCode[$apiCode];
        }

        try {
            Loader::requireModule('iblock');
            $this->prepareIblockCache($apiCode);

        } catch (Throwable $t) {
            throw new ModuleException($t->getMessage(), $t->getCode());
        }

        return $this->cacheIBlocksByApiCode[$apiCode];
    }

    /**
     * @param string $apiCode
     * @return void
     * @throws ArgumentException
     * @throws ModuleException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private function prepareIblockCache(string $apiCode): void
    {
        if ($iblockEntity = IblockTable::compileEntity($apiCode)) {
            $iblock = $iblockEntity->getIblock();

            /** @noinspection PhpUndefinedMethodInspection */
            $iblockId = $iblock->getId();

            $iblockItem = new IblockItem($iblockId, $apiCode, $iblock);

            $this->cacheIBlocksByApiCode[$apiCode] = $iblockItem;
            $this->cacheIBlocksByApiCode[$iblockId] = $iblockItem;

        } else {
            throw new ModuleException('not found iblock by API_CODE: '. $apiCode);
        }
    }

}