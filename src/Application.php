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
     * @param RequestInterface $request
     * @return string
     */
    protected function run()
    {
        $container = $this->getContainer();

        $resolver = new HMvcResolver($container->get('request'), $container->get('kernel')->getAppDir());

        return print_r($resolver, true);
    }

    protected function exception($error)
    {
        // TODO: Implement exception() method.
    }
}
