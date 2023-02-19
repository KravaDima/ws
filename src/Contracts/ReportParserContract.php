<?php

declare(strict_types=1);

namespace WS\Contracts;

interface ReportParserContract
{
    public function getData(string $vincode): string;
    public function reportIsExist(string $vincode): bool;
}
