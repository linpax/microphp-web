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
    public function run()
    {
        return print_r($this->getContainer(), true) . print_r($this->getContainer()->get('kernel'), true);
    }

    protected function exception($error)
    {
        // TODO: Implement exception() method.
    }
}
