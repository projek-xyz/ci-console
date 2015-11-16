<?php
namespace Projek\CI\Console;

use Projek\CI\Common\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {
        is_cli() or die('This class should be called via CLI only');

        parent::__construct();
    }

    public function index()
    {
        $this->load->language('console/console');

        $console_configs = [];
        if ($this->load->config('console/console', true, true)) {
            $console_configs = $this->config->item('console');
        }

        $args = func_get_args();
        $console = new Cli($console_configs);

        return $console->execute($args);
    }
}
