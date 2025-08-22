<?php

namespace Base\Module\Service\Iblock;


use Bitrix\Main\ORM\Query\Query;

interface IblockItem
{
    public function getId();
    public function getQuery(): Query;
    public function getEnumPropValues(string $enumPropCode): array;
}