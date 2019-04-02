<?php

namespace EvolveEngine\Acf\Capsule;

class FieldGroup
{
    public static $namespace = [];

    const TYPE_GROUP = 'FIELD_GROUP';
    const TYPE_REPEATER = 'REPEATER';

    /**
     * @var array Raw content of ACF definition file
     */
    protected $content;

    /**
     * @var array Valid ACF field according to ACF's standard
     */
    protected $parsed;

    /**
     * Type of field group
     *
     * @var string
     */
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Create and parse group with given content
     *
     * @param array $content
     * @return $this
     */
    public function make($content)
    {
        $this->namespace(array_get($content, 'key'), function () use ($content) {
            $this->content = $content;
            $this->parsed = tap($content, function (&$group) {
                $group = $this->parseGroup($group);
                $group[$this->getFieldsKey()] = $this->parseFields($group);
            });
        });
        return $this;
    }

    /**
     * Retrieve parsed and valid ACF array
     *
     * @return array
     */
    public function parsed()
    {
        return $this->parsed;
    }

    /**
     * Group and scope all fields to given namespace.
     * The namespace is used to prefix all fields to ensure their uniqueniess.
     *
     * @param string $namespace
     * @param Closure $callback
     * @return $this
     */
    public function namespace($namespace, $callback)
    {
        static::$namespace[] = $namespace;
        $callback($this);
        array_pop(static::$namespace);
        return $this;
    }

    /**
     * Parse group-type content
     *
     * @param array $group
     * @return array
     */
    protected function parseGroup($group)
    {
        collect([
            Rules\GroupLocationRule::class
        ])->each(function ($rule) use (&$group) {
            $group = (new $rule)->process($group);
        });
        return $group;
    }

    /**
     * Parse collection of ACF fields
     *
     * @param array $group
     * @return array
     */
    protected function parseFields($group)
    {
        $fields = [];
        $yamlFields = array_get($group, $this->getFieldsKey(), []);
        foreach ($yamlFields as $key => $value) {
            $fields[] = $this->makeField($key, $value);
        }
        return $fields;
    }

    /**
     * Creates ACF-valid field
     *
     * @param string $key
     * @param array $value
     * @return array
     */
    protected function makeField($key, $value)
    {
        $uniqueKey = $this->makeKey($key);
        $value = array_merge($value, [
            'name' => $key,
            'key'  => $uniqueKey
        ]);

        if (array_get($value, 'type') === 'repeater') {
            $value = (new FieldGroup(FieldGroup::TYPE_REPEATER))
                ->make($value)
                ->parsed();
        }

        collect([
            Rules\FieldDefaultsRule::class
        ])->each(function ($rule) use ($key, &$value) {
            $value = (new $rule)->process($key, $value);
        });

        return $value;
    }

    /**
     * Generate unique key based on current active namespace
     *
     * @param string $key
     * @return string
     */
    protected function makeKey($key)
    {
        if (count(static::$namespace) <= 0) {
            return $key;
        }
        $namespace = array_values(array_slice(static::$namespace, -1))[0];
        return $namespace . '_' . $key;
    }

    /**
     * Retrieve associative keys of fields based on active group type
     *
     * @return string
     */
    protected function getFieldsKey()
    {
        switch ($this->type) {
            case static::TYPE_GROUP:
                return 'fields';
                break;
            case static::TYPE_REPEATER:
                return 'sub_fields';
                break;
            default:
                throw new \Exception("Invalid type: $this->type");
                break;
        }
    }
}
