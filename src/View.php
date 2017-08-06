<?php
/**
 * @link https://github.com/linpax/microphp-web
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-web/blob/master/LICENSE
 */

namespace Micro\Web;


class View implements \Micro\Mvc\View
{
    /** @var string $layout */
    protected $layout;
    /** @var string $view */
    protected $view;
    /** @var string $module */
    protected $module;
    /** @var string $path */
    protected $path;
    /** @var string $data */
    protected $data;
    /** @var array $params */
    protected $params;


    public function addParameter($name, $value)
    {
        $this->params[$name] = $value;
    }
    public function getParameters()
    {
        return $this->params;
    }

    public function setLayout($name)
    {
        $this->layout = $name;
    }
    public function getLayout()
    {
        return $this->layout;
    }

    public function setView($name)
    {
        $this->view = $name;
    }
    public function getView()
    {
        return $this->view;
    }

    public function setModulePath($module)
    {
        $this->module = $module;
    }
    public function getModulePath()
    {
        return $this->module;
    }

    public function setPath($viewDir)
    {
        $this->path = $viewDir;
    }
    public function getPath()
    {
        return $this->path;
    }

    public function setData($source)
    {
        $this->data = $source;
    }
    public function getData()
    {
        return $this->data;
    }
}