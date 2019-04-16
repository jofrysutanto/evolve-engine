<?php

namespace EvolveEngine\Acf\Capsule\Rules;

class FieldConditionRule
{
    /**
     * Process this rule
     *
     * @param array $acf
     * @return array
     */
    public function process($group, $key, array $acf): array
    {
        $conditions = array_get($acf, 'conditional_logic', []);
        if (count($conditions) <= 0) {
            return $acf;
        }

        foreach ($conditions as $and => $andContent) {
            foreach ($andContent as $or => $value) {
                $keyReference = array_get($value, 'field', '');
                $path = sprintf('conditional_logic.%s.%s.field', $and, $or);
                array_set($acf, $path, $group->makeKey($keyReference));
            }
        }

        return $acf;
    }
}
