<?php

namespace EvolveEngine\Acf\Capsule\Rules;

class GroupLocationRule
{
    /**
     * Process this rule
     *
     * @param array $acf
     * @return array
     */
    public function process(array $acf): array
    {
        $location = array_get($acf, 'location');

        if (!$location) {
            return $acf;
        }

        $result = [];
        foreach ($location as $loc) {
            $result = [[$loc]];
        }
        $acf['location'] = $result;

        return $acf;
    }
}
