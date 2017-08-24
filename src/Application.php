<?php
/**
 * @link https://github.com/linpax/microphp-web
 * @copyright Copyright (c) 2017 Linpax
 * @license https://github.com/linpax/microphp-web/blob/master/LICENSE
 */

namespace Micro\Web;


/**
 * Class Application
 *
 * @package Micro\Web
 */
class Application extends \Micro\Base\Application
{
    /**
     * @return string
     * @throws \Exception
     */
    protected function run()
    {
        /** @var HMvcResolver $resolver */
        $resolver = $this->getContainer()->get('resolver');

        return $resolver->getController()->action((string)$resolver->getActionName(), $this->getContainer());
    }

    protected function exception($error)
    {
        die(var_dump($this));
    }
}
