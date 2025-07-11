<?php

namespace Base\Module\Service\Iblock;


use Bitrix\Main\ORM\Query\Query;

interface IblockItem
{
    public function getQuery(): Query;
}