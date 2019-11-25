<?php

namespace EvolveEngine\Acf\Capsule;

use EvolveEngine\Support\Singleton;

class BlueprintsFactory
{
    use Singleton;

    protected $blueprints = [];

    /**
     * Store blueprint configuration
     *
     * @param string $key
     * @param array $data
     * @return $this
     */
    public function store($key, array $data = [])
    {
        $this->blueprints[$key] = $data;
        return $this;
    }

    /**
     * Attempt to merge all fields from blueprints if available
     *
     * @param array $yamlFields
     * @return array
     */
    public function mergeBlueprints(array $yamlFields)
    {
        $result = [];
        foreach ($yamlFields as $key => $fields) {
            // Bail early if not 'blueprint' type field
            if (array_get($fields, 'type') !== 'blueprint') {
                $result[$key] = $fields;
                continue;
            }
            $result = array_merge($result, $this->unpackBlueprint($key, $fields));
        }
        return $result;
    }

    /**
     * Unpack given field blueprint
     *
     * @param string $key
     * @param array $fields
     * @return array
     */
    protected function unpackBlueprint($key, array $fields = [])
    {
        $source = array_get($fields, 'source');
        $blueprint = array_get($this->blueprints, $source);
        if (!$blueprint) {
            return [
                $key => $this->reportMissingBlueprint($source)
            ];
        }
        $cloner = new BlueprintBuilder($blueprint, $key, $fields);
        return $cloner->makeCopy();
    }

    /**
     * Create missing blueprint block message field
     *
     * @param string $name
     * @return array
     */
    protected function reportMissingBlueprint($name)
    {
        $message = sprintf('<div style="background-color: #ff7f50; padding: 10px 15px; color: #fff; border-radius: 4px;">Missing blueprint: <strong>%s</strong></div>', $name);
        return [
            'type' => 'message',
            'message' => $message,
            'esc_html' => false
        ];
    }
}
