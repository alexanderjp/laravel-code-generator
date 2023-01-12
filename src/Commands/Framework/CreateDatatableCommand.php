<?php

namespace CrestApps\CodeGenerator\Commands\Framework;

use CrestApps\CodeGenerator\Commands\Bases\DatatableCommandBase;
use CrestApps\CodeGenerator\Models\Field;
use CrestApps\CodeGenerator\Models\ForeignRelationship;
use CrestApps\CodeGenerator\Models\Resource;
use CrestApps\CodeGenerator\Support\Arr;
use CrestApps\CodeGenerator\Support\Config;
use CrestApps\CodeGenerator\Support\FieldTransformer;
use CrestApps\CodeGenerator\Support\Helpers;
use CrestApps\CodeGenerator\Support\Str;
use CrestApps\CodeGenerator\Traits\CommonCommand;
use CrestApps\CodeGenerator\Traits\GeneratorReplacers;
use CrestApps\CodeGenerator\Traits\LanguageTrait;
use Illuminate\Console\Command;

class CreateDatatableCommand extends Command
{
    use CommonCommand, GeneratorReplacers, LanguageTrait;

    /**
     * Total white-spaced to eliminate when creating an array string.
     *
     * @var string
     */
    protected $backspaceCount = 8;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:datatable
                            {datatable-name : The name of the datatable.}
                            {--resource-file= : The name of the resource-file to import from.}
                            {--language-filename= : The languages file name to put the labels in.}
                            {--datatable-directory= : The directory where the datatable should be created.}
                            {--template-name= : The template name to use when generating the code.}
                            {--force : Override the datatable if one already exists.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new datatable.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Datatable';

    /**
     * Builds the datatable class with the given name.
     *
     * @return string
     */
    public function handle()
    {
        $input = $this->getCommandInput();
        $resource = Resource::fromFile($input->resourceFile, $input->languageFileName);
        $destenationFile = $this->getDestenationFile($input->datatableName, $input->datatableDirectory);

        if ($this->hasErrors($resource, $destenationFile)) {
            return false;
        }

        $fields = $resource->fields;
  
        $stub = $this->getStubContent('datatable');
        $primaryField = $this->getPrimaryField($fields);
  
        // $relations = $this->getRelationMethods($resource->relations, $fields);
        // $namespacesToUse = $this->getRequiredUseClasses($this->getAdditionalNamespaces($input));

        $columnCreator = $this->getDatatableColumnGenerator($resources->fields, $input->modelName, $this->getTemplateName());


        return $this->replaceModelName($stub, $input->modelName)
            ->replacePrimaryKey($stub, $this->getPrimaryKeyName($resources->fields))
            ->replaceColumns($stub, $columnCreator->getColumnFields())
            ->createFile($destenationFile, $stub)
            ->info('A datatable was crafted successfully.');
            
    }


    /**
     * Replaces the datatable Columns' code in a given stub.
     *
     * @param string $stub
     * @param string $fields
     *
     * @return $this
     */
    protected function replaceColumns(&$stub, $fields)
    {
        return $this->replaceTemplate('datatable_columns_code', $fields, $stub);
    }



    /**
     * Checks for basic errors
     *
     * @param  CrestApps\CodeGenerator\Models\Resource $resource
     * @param string $destenationFile
     *
     * @return bool
     */
    protected function hasErrors(Resource $resource, $destenationFile)
    {
        $hasErrors = false;

        if ($resource->isProtected('datatable')) {
            $this->warn('The datatable is protected and cannot be regenerated. To regenerate the file, unprotect it from the resource file.');

            $hasErrors = true;
        }

        if ($this->alreadyExists($destenationFile)) {
            $this->error('The datatable already exists!');

            $hasErrors = true;
        }

        return $hasErrors;
    }

    /**
     * Gets the destenation file to be created.
     *
     * @param string $name
     * @param string $path
     *
     * @return string
     */
    protected function getDestenationFile($name, $path)
    {
        if (!empty($path)) {
            $path = Helpers::getPathWithSlash($path);
        }

        return app_path(Config::getDatatablesPath($path . $name . '.php'));
    }


    /**
     * Replaces the primary key for the given stub.
     *
     * @param  string  $stub
     * @param  string  $primaryKey
     *
     * @return $this
     */
    protected function replacePrimaryKey(&$stub, $primaryKey)
    {
        return $this->replaceTemplate('primary_key', $primaryKey, $stub);
    }




    /**
     * Gets a clean user inputs.
     *
     * @return object
     */
    protected function getCommandInput()
    {
        $modelName = trim($this->argument('model-name'));
        $primaryKey = trim($this->option('primary-key'));
        $datatableName = trim($this->option('datatable-name')) ?: Helpers::makeDatatableName($input->modelName);
        $datatableDirectory = trim($this->option('datatable-directory'));
        $resourceFile = trim($this->option('resource-file')) ?: Helpers::makeJsonFileName($modelName);
        $languageFileName = $this->option('language-filename') ?: self::makeLocaleGroup($modelName);
        $template = $this->getTemplateName();

        return (object) compact(
            'modelName',
            'primaryKey',
            'datatableName',
            'datatableDirectory',
            'resourceFile',
            'languageFileName',
            'template'
        );
    }


}
