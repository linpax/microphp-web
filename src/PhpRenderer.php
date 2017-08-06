<?php
/**
 * @link https://github.com/linpax/microphp-web
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-web/blob/master/LICENSE
 */

namespace Micro\Web;

use Micro\Mvc\Renderer;
use \Micro\Mvc\View;
use Micro\Html\Html;


class PhpRenderer implements Renderer
{
    protected $styleScripts = [];
    protected $applicationDir = '';
    protected $viewExtension = 'php';


    /**
     * PhpRenderer constructor.
     * @param string $appDir
     */
    public function __construct($appDir = '', $viewExtension = 'php')
    {
        $this->applicationDir = $appDir;
        $this->viewExtension = $viewExtension;
    }

    /**
     * @param View $view
     * @return string
     */
    public function render(View $view)
    {
        if (!$view->getPath() || !$view->getView()) {
            return false;
        }

        $viewFilename = $view->getPath().'/'.$view->getView().'.'.ltrim($this->viewExtension, '.');
        if (!file_exists($viewFilename)) { /// ALARM
            return false;
        }

        $layout = null;
        if (
            $view->getLayout()
            &&
            (!$layout = $this->getLayoutFile($this->applicationDir, $view->getModulePath(), $view->getLayout()))
        ) {
            throw new \Exception('Layout `'.$layout.'` not found');
        }

        $source = $view->getData();
        if ($source) {
            return $this->renderRawData($source, $layout);
        }

        return $this->renderRawData($this->renderFile($viewFilename, $view->getParameters()), $layout);
    }
    public function renderRawData($source, $layout = null)
    {
        if ($layout) {
            $source = $this->insertStyleScripts($this->renderFile($layout, ['content' => $source]));
        }

        return $source;
    }
    protected function getLayoutFile($appDir, $module, $layoutName)
    {
        if ($module) {
            $module = strtolower(str_replace('\\', '/', $module));
            $module = substr($module, strpos($module, '/') + 1);
            $module = substr($module, 0, strrpos($module, '/'));
        }

        $layout = $appDir.'/'.($module ? $module.'/' : $module);
        $afterPath = 'views/layouts/'.ucfirst($layoutName).'.php';

        if (file_exists($layout.$afterPath)) {
            return $layout.$afterPath;
        }

        if (file_exists($appDir.'/'.$afterPath)) {
            return $appDir.'/'.$afterPath;
        }

        return false;
    }

    protected function renderFile($fileName, array $data = [])
    {
        extract($data, EXTR_PREFIX_SAME || EXTR_REFS, 'data');
        ob_start();

        /** @noinspection PhpIncludeInspection */
        include str_replace('\\', '/', $fileName);

        if (!empty($GLOBALS['widgetStack'])) {
            throw new \Exception(count($GLOBALS['widgetStack']).' widgets not endings.');
        }

        return ob_get_clean();
    }


    public function widget($name, $options = [], $capture = false)
    {
        if (!class_exists($name)) {
            throw new \Exception('Widget '.$name.' not found.');
        }

        /** @var \Micro\Mvc\Widget $widget widget */
        $widget = new $name($options);
        $widget->init();

        if ($capture) {
            ob_start();
            $widget->run();
            $result = ob_get_clean();
        } else {
            $result = $widget->run();
        }

        if ($result instanceof View) {
            $result->asWidget = true;
            $result->path = get_class($widget);

            $result = $this->render($result);
        }

        unset($widget);

        if ($capture) {
            return $result;
        }

        echo $result;

        return '';
    }
    public function beginWidget($name, $options = [])
    {
        if (!class_exists($name)) {
            throw new \Exception('Widget `'.$name.'` not found.');
        }

        if (!empty($GLOBALS['widgetStack'][$name])) {
            throw new \Exception('This widget `'.$name.'` already started!');
        }

        $GLOBALS['widgetStack'][$name] = new $name($options);

        /** @noinspection PhpUndefinedMethodInspection */

        return $GLOBALS['widgetStack'][$name]->init();
    }
    public function endWidget($name = null)
    {
        if (!$name && $GLOBALS['widgetStack']) {
            /** @var \Micro\Mvc\Widget $widget */
            $widget = array_pop($GLOBALS['widgetStack']);
            $v = $widget->run();

            if ($v instanceof View) {
                $v->asWidget = true;
                $v->path = get_class($widget);

                $v = $this->render($v);
            }

            unset($widget);
            echo $v;
        }

        if (empty($GLOBALS['widgetStack'][$name]) && !class_exists($name)) {
            throw new \Exception('Widget `'.$name.'` not started.');
        }

        /** @var \Micro\Mvc\Widget $widget widget */
        $widget = $GLOBALS['widgetStack'][$name];
        unset($GLOBALS['widgetStack'][$name]);

        $v = $widget->run();

        if ($v instanceof View) {
            $v->asWidget = true;
            $v->path = get_class($widget);

            $v = $this->render($v);
        }

        unset($widget);
        echo $v;
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
}