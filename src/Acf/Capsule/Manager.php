<?php

namespace EvolveEngine\Acf\Capsule;

class Manager
{
    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @var BlueprintsFactory
     */
    protected $blueprints;

    public function __construct()
    {
        $this->finder = new Finder;
        $this->blueprints = BlueprintsFactory::instance();
    }

    /**
     * Register our in-code ACF fields
     *
     * @return void
     */
    public function register()
    {
        // Collect all reusable blueprints
        $this->storeBlueprints(
            $this->getDefinitions('blueprints')
        );

        foreach (['fields', 'pages'] as $type) {
            collect($this->getDefinitions($type))
                ->each(function ($def) {
                    $parsed = $this->read($def);
                    acf_add_local_field_group($parsed);
                });
        }
    }

    /**
     * Read an ACF definition file
     *
     * @param string $def
     * @return array Array containing ACF fields as-per ACF definitions
     */
    public function read($def)
    {
        $content = $this->finder->read($def);

        $result = (new FieldGroup(FieldGroup::TYPE_FIELD_GROUP))
            ->make($content)
            ->parsed();

        return $result;
    }

    /**
     * Store blueprints data
     *
     * @param array $data
     * @return void
     */
    protected function storeBlueprints(array $data = [])
    {
        foreach ($data as $def) {
            $content = $this->finder->read($def);
            $key = str_replace('.yaml', '', basename($def));
            $this->blueprints->store($key, $content);
        }
    }

    /**
     * Retrieve locations of ACF definitions
     *
     * @return array
     */
    protected function getDefinitions($type)
    {
        try {
            $fields = $this->finder->index();
            return array_get($fields, $type, []);
        } catch (\Throwable $th) {
            return [];
        }
    }
}
