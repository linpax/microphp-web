<?php
/**
 * @link https://github.com/linpax/microphp-web
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-web/blob/master/LICENSE
 */

namespace Micro\Web;


use Micro\Mvc\Render;

class PhpRender implements Render
{
    public function rendering($source, array $params = [])
    {
        return $source;
    }
}