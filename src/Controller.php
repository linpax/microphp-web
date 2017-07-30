<?php
/**
 * @link https://github.com/linpax/microphp-web
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-web/blob/master/LICENSE
 */

namespace Micro\Web;

use Micro\Base\Container;


class Controller implements \Micro\Mvc\Controller
{
    /** @var Container $container */
    protected $container;

    public function action($name, $container)
    {
        $this->container = $container;

        return $this->{'action'.ucfirst($name)}();
    }
}