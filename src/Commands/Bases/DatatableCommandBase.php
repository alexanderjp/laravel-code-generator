<?php

namespace CrestApps\CodeGenerator\Commands\Bases;

// use CrestApps\CodeGenerator\Traits\Migration;
use CrestApps\CodeGenerator\DatatableColumnGenerators\StandardColumn;
use CrestApps\CodeGenerator\Models\Resource;
use CrestApps\CodeGenerator\Models\ViewInput;
use CrestApps\CodeGenerator\Support\Config;
use CrestApps\CodeGenerator\Support\Helpers;
use CrestApps\CodeGenerator\Support\Str;
use CrestApps\CodeGenerator\Support\ViewLabelsGenerator;
use CrestApps\CodeGenerator\Traits\CommonCommand;
use CrestApps\CodeGenerator\Traits\GeneratorReplacers;
use Illuminate\Console\Command;

class DatatableCommandBase extends Command
{

    /**
     * Gets a new instance of the proper html generator.
     *
     * @param array $fields
     * @param string $modelName
     * @param string $template
     *
     * @return CrestApps\CodeGenerator\HtmlGenerators\HtmlGeneratorBase
     */
    protected function getDatatableColumnGenerator(array $fields, $modelName, $template)
    {
        return new StandardColumn($fields, $modelName, $template);
    }

    /**
     * Gets the correct primary key name.
     *
     * @param CreatApps\CodeGenerator\Models\Field $primaryField
     * @param string $primaryKey
     *
     * @return string
     */
    protected function getPrimaryKeyName(Field $primaryField = null, $primaryKey = 'id')
    {
        return !is_null($primaryField) ? $primaryField->name : $primaryKey;
    }


}
