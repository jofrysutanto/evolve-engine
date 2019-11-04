<?php

namespace EvolveEngine\Acf\Capsule;

use EvolveEngine\Support\Singleton;

class TemplatesFactory
{
    use Singleton;

    protected $templates = [];

    /**
     * Store template configuration
     *
     * @param string $key
     * @param array $data
     * @return $this
     */
    public function store($key, array $data = [])
    {
        $this->templates[$key] = $data;
        return $this;
    }

    /**
     * Attempt to merge all fields from templates if available
     *
     * @param array $yamlFields
     * @return array
     */
    public function mergeTemplates(array $yamlFields)
    {
        $result = [];
        foreach ($yamlFields as $key => $fields) {
            // Bail early if not 'template' type field
            if (array_get($fields, 'type') !== 'template') {
                $result[$key] = $fields;
                continue;
            }
            $result = array_merge($result, $this->unpackFieldTemplate($key, $fields));
        }
        return $result;
    }

    /**
     * Unpack given field template
     *
     * @param string $key
     * @param array $fields
     * @return array
     */
    protected function unpackFieldTemplate($key, array $fields = [])
    {
        $source = array_get($fields, 'source');
        $template = array_get($this->templates, $source);
        if (!$template) {
            return [
                $key => $this->reportMissingTemplate($source)
            ];
        }
        $cloner = new TemplateCloner($template, $key, $fields);
        return $cloner->makeCopy();
    }

    /**
     * Create missing template block message field
     *
     * @param string $name
     * @return array
     */
    protected function reportMissingTemplate($name)
    {
        $message = sprintf('<div style="background-color: #ff7f50; padding: 10px 15px; color: #fff; border-radius: 4px;">Missing template: <strong>%s</strong></div>', $name);
        return [
            'type' => 'message',
            'message' => $message,
            'esc_html' => false
        ];
    }
}
