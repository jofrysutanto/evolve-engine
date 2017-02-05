<?php

namespace EvolveEngine\Acf;

class Extender
{   
    /**
     * Array of extend classes
     *
     * @var array
     */
    protected $extensions = [];

    /**
     * Flag to determine if extensions have been registered
     *
     * @var boolean
     */
    protected $isRegistered = false;

    /**
     * Register new field class to acf
     *
     * @return $this
     */
    public function extendField($class)
    {
        $this->extensions[] = $class;
        return $this;
    }

    /**
     * Register all extensions
     *
     * @return $this
     */
    public function register()
    {
        foreach ($this->extensions as $extendClass) {
            // Based on how ACF works, just creating new instance
            // of extension is enough, all extensions inherits from acf_field
            new $extendClass;
        }
        $this->isRegistered = true;
        return $this;
    }

    /**
     * Merge content fields into custom
     *
     * @return  void
     */
    public function seamlessContentFields()
    {
        ?>
            <script type="text/javascript">
            (function($) {
                
                $(document).ready(function() {
                    if ($('#postdivrich').length && $('#seamless').length) {
                        $('#postdivrich').appendTo($('#seamless .acf-input'))
                    }
                });
                
            })(jQuery);    
            </script>
            <style type="text/css">
                .acf-field #wp-content-editor-tools {
                    background: transparent;
                    padding-top: 0;
                }
            </style>
            <?php    
    }

}
