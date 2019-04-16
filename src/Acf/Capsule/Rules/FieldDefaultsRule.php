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
    public function process($group, $key, array $acf): array
    {
        $type = array_get($acf, 'type');
        if (!$type) {
            $acf['type'] = 'text';
        }
        // Shortcut to width
        if ($width = array_get($acf, 'width')) {
            unset($acf['width']);
            array_set($acf, 'wrapper.width', $width);
        }
        // Shortcut to class
        if ($class = array_get($acf, 'class')) {
            unset($acf['class']);
            array_set($acf, 'wrapper.class', $class);
        }
        // Typo resistance
        // - instruction -> instructions
        if ($instruction = array_get($acf, 'instruction')) {
            array_set($acf, 'instructions', $instruction);
            unset($acf['instruction']);
        }
        // Image instruction sippet
        if ($type === 'image' && $recommmended = array_get($acf, 'recommended')) {
            $instruction = $this->getRecommendHtml($recommmended) . '<br>' . array_get($acf, 'instructions');
            array_set($acf, 'instructions', $instruction);
        }
        return $acf;
    }

    /**
     * Decorate recommended width height
     *
     * @param array $recommmended
     * @return string
     */
    protected function getRecommendHtml($recommmended)
    {
        return sprintf(
            '<span class="acf-recommended-size" title="Recommended image size (width and height in pixels)"><span><strong>Size:</strong> %spx by %spx</span></span>',
            $recommmended['width'],
            $recommmended['height']
        );
    }
}
