<?php

namespace App\Imports\Columns;

use RedSquirrelStudio\LaravelBackpackImportOperation\Columns\ImportColumn;

class Engines extends ImportColumn
{
    /**
     * @return ?string
     */
    public function output(): mixed
    {
        return $this->data;
    }
}
