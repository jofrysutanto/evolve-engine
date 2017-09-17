<?php
namespace EvolveEngine\Views;

use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

class ParamsFactory 
{

    /**
     * @var array
     */
    protected $fields;

    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
    }

    /**
     * Parse given variable array, based on existing fields configuration
     *
     * @param  array  $vars
     *
     * @return array
     */
    public function parse(array $vars = [])
    {
        foreach ($this->fields as $fieldKey => $field) {
            $isVarExists = isset($vars[$fieldKey]);
            $field = $this->assignDefaultFieldValues($field);
            $var   = null;

            if ($isVarExists) {
                continue;
            }
            // Check for default
            if ($valueFrom = $field->valueFrom) {
                $var = $this->getValueFrom($valueFrom);
            }
            else {
                $var = $this->getDefaultValue($field->default);
            }

            // Assign back to the array
            $vars[$fieldKey] = $var;
        }

        return $vars;
    }

    protected function assignDefaultFieldValues($field)
    {
        $default = [
            'default'  => null,
            'required' => false,
            'valueFrom' => null
        ];
        $merged = array_merge($default, $field);
        $fluent = new Fluent($merged);
        return $fluent;
    }

    protected function getDefaultValue($default = '')
    {
        if (Str::startsWith($default, '~')) {
            return asset(substr($default, 1));
        }

        return $default;
    }

    protected function getValueFrom($valueFrom)
    {
        list($concrete, $method) = explode('@', $valueFrom);
        $instance = app($concrete);
        if (!$instance) {
            return null;
        }
        return $instance->{$method}();
    }

}