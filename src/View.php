<?php
/**
 * @link https://github.com/linpax/microphp-web
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-web/blob/master/LICENSE
 */

namespace Micro\Web;

use Micro\Html\Html;


class View implements \Micro\Mvc\View
{
    /** @var array $params */
    protected $params;
    /** @var array $styleScripts */
    protected $styleScripts;


    public function addParameter($name, $value)
    {
        $this->params[$name] = $value;
    }
    public function getParameters()
    {
        return $this->params;
    }


    /**
     * Insert styles and scripts into cache
     *
     * @access protected
     *
     * @param string $cache cache of generated page
     *
     * @return string
     */
    protected function insertStyleScripts($cache)
    {
        $heads = '';
        $ends = '';
        $result = '';

        foreach ($this->styleScripts AS $element) {
            if ($element['isHead']) {
                $heads .= $element['body'];
            } else {
                $ends .= $element['body'];
            }
        }

        $positionHead = strpos($cache, Html::closeTag('head'));
        $positionBody = strpos($cache, Html::closeTag('body'), $positionHead);

        $result .= substr($cache, 0, $positionHead);
        $result .= $heads;
        $result .= substr($cache, $positionHead, $positionBody);
        $result .= $ends;
        $result .= substr($cache, $positionHead + $positionBody);

        return $result;
    }


    /**
     * Register JS script
     *
     * @access public
     *
     * @param string $source file name
     * @param bool $isHead is head block
     *
     * @return void
     */
    public function registerScript($source, $isHead = true)
    {
        $this->styleScripts[] = [
            'isHead' => $isHead,
            'body' => Html::script($source)
        ];
    }

    /**
     * Register JS file
     *
     * @access public
     *
     * @param string $source file name
     * @param bool $isHead is head block
     *
     * @return void
     */
    public function registerScriptFile($source, $isHead = true)
    {
        $this->styleScripts[] = [
            'isHead' => $isHead,
            'body' => Html::scriptFile($source)
        ];
    }

    /**
     * Register CSS code
     *
     * @access public
     *
     * @param string $source file name
     * @param bool $isHead is head block
     *
     * @return void
     */
    public function registerCss($source, $isHead = true)
    {
        $this->styleScripts[] = [
            'isHead' => $isHead,
            'body' => Html::css($source)
        ];
    }

    /**
     * Register CSS file
     *
     * @access public
     *
     * @param string $source file name
     * @param bool $isHead is head block
     *
     * @return void
     */
    public function registerCssFile($source, $isHead = true)
    {
        $this->styleScripts[] = [
            'isHead' => $isHead,
            'body' => Html::cssFile($source)
        ];
    }
}