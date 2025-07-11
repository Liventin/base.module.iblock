<?php

namespace Base\Module\Service\Iblock;

interface IblockService
{
    public const SERVICE_CODE = 'base.module.iblock.service';
    public function getByApiCode(string $apiCode): object;
}