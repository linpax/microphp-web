<?php

namespace Micro\Web;

use Micro\Base\Resolver;
use Micro\Router\Web\Router;
use Psr\Http\Message\RequestInterface;


class HMvcResolver implements Resolver
{
    /** @var RequestInterface $request */
    private $request;
    /** @var string $appDir */
    private $appDir;


    /** @var string $uri converted URL */
    protected $uri;

    /** @var string $extensions Extensions in request */
    private $extensions;
    /** @var string $modules Modules in request */
    private $modules;
    /** @var string $controller IController to run */
    private $controller;
    /** @var string $action Action to run */
    private $action;


    /**
     * HMvcResolver constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request, $appDir)
    { // app dir //query params
        $this->appDir = $appDir;
        $this->request = $request;
    }

    /**
     * Get instance application
     *
     * @access public
     *
     * @return \Micro\Mvc\Controller
     * @throws \Exception
     */
    public function getController()
    {
        $rawQuery = [];
        parse_str($this->request->getUri()->getQuery(), $rawQuery);

        $query = !empty($rawQuery['r']) ? $rawQuery['r'] : '/default';
        $query = (substr($query, -1) === '/') ? substr($query, 0, -1) : $query;

        $this->uri = (new Router(
        ))->parse($query, $this->request->getMethod());

        $this->initialize();

        /** @var string $cls */
        $cls = $this->getCalculatePath();

        if (!class_exists($cls) || !is_subclass_of($cls, '\Micro\Mvc\Controller')) {
            throw new \Exception('Controller '.$cls.' not found or not a valid');
        }

        return new $cls($this->getModules());
    }

    /**
     * Initialize request object
     *
     * @access public
     *
     * @return void
     * @throws Exception
     */
    protected function initialize()
    {
        $key = strpos($this->uri, '?');
        $params = $key ? substr($this->uri, $key + 2) : null;
        $uriBlocks = explode('/', substr($this->uri, 0, $key ?: strlen($this->uri)));

        if (0 === strpos($this->uri, '/')) {
            array_shift($uriBlocks);
        }

        $this->prepareExtensions($uriBlocks);
        $this->prepareModules($uriBlocks);
        $this->prepareController($uriBlocks);
        $this->prepareAction($uriBlocks);

        if ($params) {
            $query = [];
            parse_str($this->request->getUri()->getQuery(), $query);


            $paramBlocks = explode('&', $params);
            foreach ($paramBlocks AS $param) {
                $val = explode('=', $param);

                $query[$val[0]] = $val[1];
            }

            $this->request = $this->request->withUri(
                $this->request->getUri()->withQuery(
                    http_build_query($query)
                )
            );
        }
    }

    /**
     * Prepare extensions
     *
     * @access private
     *
     * @param array $uriBlocks uri blocks from URL
     *
     * @return void
     * @throws Exception
     */
    protected function prepareExtensions(&$uriBlocks)
    {
        foreach ($uriBlocks as $i => $block) {
            if (file_exists($this->appDir.$this->extensions.'/extensions/'.$block)) {
                $this->extensions .= '/Extensions/'.ucfirst($block);

                unset($uriBlocks[$i]);
            } else {
                break;
            }
        }

        $this->extensions = str_replace('/', '\\', $this->extensions);
    }

    /**
     * Prepare modules
     *
     * @access private
     *
     * @global      Micro
     *
     * @param array $uriBlocks uri blocks from URL
     *
     * @return void
     * @throws Exception
     */
    protected function prepareModules(&$uriBlocks)
    {
        $path = $this->appDir.($this->extensions ?: '');

        foreach ($uriBlocks as $i => $block) {
            if ($block && file_exists($path.strtolower($this->modules).'/modules/'.$block)) {
                $this->modules .= '/Modules/'.ucfirst($block);

                unset($uriBlocks[$i]);
            } else {
                break;
            }
        }

        $this->modules = str_replace('/', '\\', $this->modules);
    }

    /**
     * Prepare controller
     *
     * @access private
     *
     * @param array $uriBlocks uri blocks from URL
     *
     * @return void
     * @throws Exception
     */
    protected function prepareController(&$uriBlocks)
    {
        $path = $this->appDir.($this->extensions ?: '').strtolower($this->modules ?: '');
        $str = array_shift($uriBlocks);

        if (file_exists(str_replace('\\', '/', $path.'/controllers/'.ucfirst($str).'Controller.php'))) {
            $this->controller = $str;
        } else {
            $this->controller = 'default';

            array_unshift($uriBlocks, $str);
        }
    }

    /**
     * Prepare action
     *
     * @access private
     *
     * @param array $uriBlocks uri blocks from URL
     *
     * @return void
     */
    protected function prepareAction(&$uriBlocks)
    {
        $this->action = array_shift($uriBlocks) ?: 'index';
    }


    /**
     * Get calculate path to controller
     *
     * @access public
     *
     * @return string
     */
    public function getCalculatePath()
    {
        return '\\App'.$this->getExtensions().$this->getModules().'\\Controllers\\'.$this->getControllerName();
    }

    /**
     * Get extensions from request
     *
     * @access public
     *
     * @return string
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Get modules from request
     *
     * @access public
     *
     * @return string
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Get controller from request
     *
     * @access public
     *
     * @return string
     */
    public function getControllerName()
    {
        return ucfirst($this->controller).'Controller';
    }

    /**
     * Get action from request
     *
     * @access public
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->action;
    }
}
