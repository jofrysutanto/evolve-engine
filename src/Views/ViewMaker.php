<?php
namespace EvolveEngine\Views;

class ViewMaker {

    /**
     * @var array  Shared variables
     */
    protected $vars = array();

    /**
     * @var string Path to view files
     */
    protected $viewRoot;

    public function __construct($basePath)
    {
        $this->viewRoot = $basePath;
    }

    /**
     * Output HTML string representation of given template
     *
     * @param  string  $templateName
     * @param  array   $extraVars    Additional parameters
     * @param  boolean $overrideRoot override template root path
     *
     * @return void
     */
    public function render($templateName, $extraVars = array(), $overrideRoot = false) {
        $path = $this->getPath($templateName . '.php', $overrideRoot);
        $yamlPath = $this->getPath($templateName . '.yaml', $overrideRoot);
        if (file_exists($yamlPath)) {
            $extraVars = $this->processYamlVariables($yamlPath, $extraVars);
        }

        $this->load($path, $extraVars);
    }

    /**
     * Return HTML string representation of given template
     *
     * @param  string  $templateName
     * @param  array   $extraVars    Additional parameters
     * @param  boolean $overrideRoot override template root path
     *
     * @return string
     */
    public function make($templateName, $extraVars = array(), $overrideRoot = false) {
        $path = $this->getPath($templateName . '.php', $overrideRoot);
        $yamlPath = $this->getPath($templateName . '.yaml', $overrideRoot);
        if (file_exists($yamlPath)) {
            $extraVars = $this->processYamlVariables($yamlPath, $extraVars);
        }

        ob_start();

        $this->load($path, $extraVars);

        return ob_get_clean();
    }

    /**
     * Safely check if a template can be loaded. Throw error
     * when can't be found.
     * This method outputs HTML
     * @param  string $path [description]
     * @return void
     */
    protected function load($path, $extraVars = array())
    {
        /**
         * Locate Template ensure we can still use our variables in the template files
         * @see http://keithdevon.com/passing-variables-to-get_template_part-in-wordpress/
         */
        if (count($this->vars) > 0) {
            extract($this->vars);
        }

        // Extra vars which can be passed from anywhere to the template
        if (count($extraVars) > 0) {
            extract($extraVars);
        }

        if (file_exists($path)) {
            require($path);
        } else {
            ?>
                <style>
                div.view-err-container {
                    background: none repeat scroll 0% 0% #F4726D; padding: 9px 15px;
                    overflow: hidden;
                    position: relative;
                    text-align: left;
                    margin-top: 15px; margin-bottom: 15px;
                    max-width: 960px; margin-left: auto; margin-right: auto;
                } 
                h5.view-err {
                    color: #FFF;
                    font-weight: 400 !important;
                    font-size: 13px;
                    margin-bottom: 5px;
                    margin-top: 0.5em;
                    text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.2);
                }
                </style>
                <div class="view-err-container">
                    <?php if (defined(WP_DEBUG) && WP_DEBUG === true): ?>
                    <h5 class="view-err"><strong>Missing template</strong></h5>
                    <?php else: ?>
                    <h5 class="view-err"><strong>Error</strong>: Template <strong><?= $path ?></strong> is not found or cannot be loaded.</h5>
                    <?php endif ?>
                </div>
            <?php
        }
    }

    /**
     * Check if given view exists
     *
     * @param  string  $templateName
     * @param  boolean $overrideRoot
     *
     * @return boolean
     */
    public function exists($templateName, $overrideRoot = false)
    {
        $path = $this->getPath($templateName . '.php', $overrideRoot);

        return file_exists($path);
    }

    /**
     * Share variable to wordpress page template
     * The opposite method View::extract() needs to be called at the top of 
     * wordpress template
     * @return [type] [description]
     */
    public function share($arr, $value = '')
    {
        if(!is_array($arr))
        {
            $arr = array($arr => $value);
        }

        $this->vars = array_merge_recursive($this->vars, $arr);
        return;
    }

    /**
     * Retrieve available variables from current engine
     *
     * @return array
     */
    public function vars()
    {
        return $this->vars;
    }

    /**
     * Get path to template file
     *
     * @param  string  $relativePath
     * @param  boolean $overrideRoot  If overriden, assume $relativePath is absolute path
     *
     * @return string
     */
    protected function getPath($relativePath, $overrideRoot = false)
    {
        $path = $overrideRoot ?
            $relativePath :
            $this->viewRoot . $relativePath;

        return $path;
    }

    /**
     * Parse YAMl config at given $path to merge values into $vars
     *
     * @param  string $path
     * @param  array  $vars
     *
     * @return array
     */
    protected function processYamlVariables($path, $vars)
    {
        $parser = new \Symfony\Component\Yaml\Parser;
        $varConfig = $parser->parse(file_get_contents($path));
        $fields = array_get($varConfig, 'fields');
        if (!$fields) {
            return;
        }
        $vars = with(new ParamsFactory($fields))->parse($vars);
        return $vars;
    }

}