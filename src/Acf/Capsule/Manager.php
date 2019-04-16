<?php

namespace EvolveEngine\Acf\Capsule;

class Manager
{
    /**
     * @var Finder
     */
    protected $finder;

    public function __construct()
    {
        $this->finder = new Finder;
    }

    /**
     * Register our in-code ACF fields
     *
     * @return void
     */
    public function register()
    {
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
