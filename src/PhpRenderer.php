<?php
/**
 * @link https://github.com/linpax/microphp-web
 * @copyright Copyright &copy; 2017 Linpax
 * @license https://github.com/linpax/microphp-web/blob/master/LICENSE
 */

namespace Micro\Web;

use Micro\Mvc\Renderer;
use \Micro\Mvc\View;


class PhpRenderer implements Renderer
{
    /**
     */
    public function __construct()
    {
    }

    /**
     * @param View $view
     * @return string
     */
    public function render(View $view)
    {
        return json_encode($view);
    }
}