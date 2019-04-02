<?php

namespace EvolveEngine\Acf\Capsule\Rules;

class FieldDefaultsRule
{
    /**
     * Process this rule
     *
     * @param array $acf
     * @return array
     */
    public function process($key, array $acf): array
    {
        $type = array_get($acf, 'type');
        if (!$type) {
            $acf['type'] = 'text';
        }
        return $acf;
    }
}
