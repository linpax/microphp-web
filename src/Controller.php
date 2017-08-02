<?php
/**
 * @link https://github.com/linpax/microphp-web
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-web/blob/master/LICENSE
 */

namespace Micro\Web;

use Micro\Base\Container;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;


class Controller implements \Micro\Mvc\Controller
{
    /** @var Container $container */
    protected $container;
    /** @var \Micro\Mvc\Module $module */
    protected $module;
    /** @var ResponseInterface $response */
    protected $response;
    /** @var \Micro\Mvc\View $view */
    protected $view;


    public function __construct($module = '')
    {
        if ($module) {
            $className = '\\App'.$module.'\\'.ucfirst(basename(str_replace('\\', '/', $module))).'Module';

            if (class_exists($className) && is_subclass_of($className, '\Micro\Mvc\Module')) {
                $this->module = new $className();
            }
        }
    }

    public function beforeAction()
    {
        return true;
    }
    public function afterAction()
    {
    }

    public function action($name, $container)
    {
        $this->container = $container;

        $this->view = $this->container->get('view') ?: new View;
        $this->response = $this->container->get('response') ?: new Response;

        if ($this->beforeAction()) {
            $this->{'action'.$name}(); // REAL WORK
            $this->afterAction();
        }

        return $this->response->withBody($this->container->get('renderer')->render( $this->view));
    }
}