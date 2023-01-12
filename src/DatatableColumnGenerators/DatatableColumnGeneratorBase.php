<?php

// Copy of src/HtmlGenerators/HtmlGeneratorBase.php

namespace CrestApps\CodeGenerator\DatatableColumnGenerators;

use CrestApps\CodeGenerator\Models\Field;
use CrestApps\CodeGenerator\Models\Label;
use CrestApps\CodeGenerator\Support\ValidationParser;
use CrestApps\CodeGenerator\Traits\CommonCommand;
use CrestApps\CodeGenerator\Traits\GeneratorReplacers;
use Exception;

abstract class DatatableColumnGeneratorBase
{
    use CommonCommand, GeneratorReplacers;

    /**
     * Array of fields.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Model name.
     *
     * @var string
     */
    protected $modelName;

    /**
     * Template name.
     *
     * @var string
     */
    protected $template;

    /**
     * The view Label generator.
     *
     * @var CrestApps\CodeGenerator\Support\ViewLabelsGenerator
     */
    protected $viewLabels;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(array $fields, $modelName, $template = null)
    {
        $this->modelName = $modelName;
        $this->fields = $fields;
        $this->template = $template;
        // $this->viewLabels = $this->getViewLabelsGenerator();
    }

    /**
     * Gets html field for the current set fields.
     *
     * @return string
     */
    public function getColumnFields()
    {
        $datatableFields = '';

        foreach ($this->fields as $field) {
            if (!$field->isOnFormView) {
                continue;
            }

            $parser = new ValidationParser($field->validationRules);

            // text
            // number
            // date
            // time


            if (in_array($field->columnType =='number')) {
                $datatableFields .= $this->getNumberColumn($field, $parser);
            } elseif (in_array($field->columnType == 'date')) {
                $datatableFields .= $this->getDateColumn($field, $parser);
            } elseif ($field->columnType == 'time') {
                $datatableFields .= $this->getTimeColumnField($field, $parser);
            } else {
                $datatableFields .= $this->getTextColumnField($field, $parser);
            }
        }

        // $this->replaceStandardLabels($datatableFields, $this->viewLabels->getLabels());

        return $datatableFields;
    }

    /**
     * Gets a selectmonth element for a given field.
     *
     * @param CrestApps\CodeGeneraotor\Support\Field $field
     *
     * @return string
     */
    public function getNumberColumn(Field $field)
    {
        $stub = $this->getStubContent('datatable-number-column', $this->template);

        $this->replaceCommonTemplates($stub, $field->name);

        return $stub;
    }


    /**
     * Gets a selectmonth element for a given field.
     *
     * @param CrestApps\CodeGeneraotor\Support\Field $field
     *
     * @return string
     */
    public function getDateColumn(Field $field)
    {
        $stub = $this->getStubContent('datatable-date-column', $this->template);

        $this->replaceCommonTemplates($stub, $field->name);

        return $stub;
    }

    /**
     * Gets creates an standard html5 field for a given field.
     *
     * @param CrestApps\CodeGeneraotor\Support\Field $field
     * @param CrestApps\CodeGeneraotor\Support\ValidationParser $parser
     *
     * @return string
     */
    public function getTimeColumnField(Field $field, ValidationParser $parser)
    {
        $stub = $this->getStubContent('datatable-time-column', $this->template);

        $this->replaceCommonTemplates($stub, $field->name);

        return $stub;
    }


    /**
     * Gets creates an textarea html field for a given field.
     *
     * @param CrestApps\CodeGeneraotor\Support\Field $field
     * @param CrestApps\CodeGeneraotor\Support\ValidationParser $parser
     *
     * @return string
     */
    protected function getTextColumnField(Field $field, ValidationParser $parser)
    {
        $stub = $this->getStubContent('datatable-text-column', $this->template);

        $this->replaceCommonTemplates($stub, $field->name);

        return $stub;
    }



    /**
     * Replaces field's common templates
     *
     * @param CrestApps\CodeGenerator\Models\Field $field
     *
     * @return $this
     */
    public function replaceCommonTemplates(&$stub, Field $field)
    {
        return $this->replaceFieldName($stub, $field->name)
            ->replaceModelName($stub, $this->modelName)
            ->replaceFieldTitle($stub, $this->getTitle($field->getLabel()));
    }




    /**
     * Replace the fieldTitle fo the given stub.
     *
     * @param string $stub
     * @param CrestApps\CodeGenerator\Models\Label $label
     * @param string $fieldTitle
     *
     * @return $this
     */
    protected function replaceFieldTitle(&$stub, $title)
    {
        $stub = $this->strReplace('field_title', $title, $stub);

        return $this;
    }

    /**
     * Replace the fieldName fo the given stub.
     *
     * @param string $stub
     * @param string $fieldName
     *
     * @return $this
     */
    protected function replaceFieldName(&$stub, $fieldName)
    {
        $stub = $this->strReplace('field_name', $fieldName, $stub);

        return $this;
    }



    /**
     * Gets title to display from a given label.
     *
     * @param CrestApps\CodeGenerator\Models\Label $label
     * @param bool $raw
     *
     * @return $this
     */
    protected function getTitle(Label $label, $raw = false)
    {
        if (!$label->isPlain) {
            return $this->getTranslatedTitle($label, $raw);
        }

        return $this->getPlainTitle($label, $raw);
    }
   
    /**
     * Gets title in trans() method.
     *
     * @param CrestApps\CodeGenerator\Models\Label $label
     * @param bool $raw
     *
     * @return string
     */
    protected function getTranslatedTitle(Label $label, $raw = false)
    {
        $template = $raw === false ? "trans('%s')" : "{{ trans('%s') }}";

        return sprintf($template, $label->getAccessor());
    }


    

    


    

    


    
}
