<?php
/**
 * @link https://github.com/linpax/microphp-web
 * @copyright Copyright (c) 2017 Linpax
 * @license https://github.com/linpax/microphp-web/blob/master/LICENSE
 */

namespace Micro\Web;

use Micro\Mvc\Controller;


/**
 * Class Application
 *
 * @package Micro\Web
 */
class Application extends \Micro\Base\Application
{
    /**
     * @return string
     */
    protected function run()
    {
        /** @var HMvcResolver $resolver */
        $resolver = $this->getContainer()->get('resolver');
        /** @var Controller $controller */
        $controller = $resolver->getController();

        return $controller->action((string)$resolver->getActionName(), $this->getContainer());
    }

    protected function exception($error)
    {
        // TODO: Implement exception() method.
    }
}
