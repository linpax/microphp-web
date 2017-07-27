<?php
/**
 * @link https://github.com/linpax/microphp-web
 * @copyright Copyright (c) 2017 Linpax
 * @license https://github.com/linpax/microphp-web/blob/master/LICENSE
 */

namespace Micro\Web;

use Psr\Http\Message\RequestInterface;


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
        $resolver = $this->getContainer()->get('resolver');

        $app = $resolver->getApp();
        $action = $resolver->getAction();

        return $app->action((string)$action);
    }

    protected function exception($error)
    {
        // TODO: Implement exception() method.
    }
}
