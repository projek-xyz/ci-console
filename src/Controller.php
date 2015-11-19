<?php
namespace Projek\CI\Console;

use Projek\CI\Common\Controller as BaseController;

class Controller extends BaseController
{
    private $cli;

    protected $available_commands = [];

    public function __construct()
    {
        is_cli() or die('This class should be called via CLI only');

        parent::__construct();

        $this->load->language('console/console');

        $configs = [];
        if ($this->load->config('console/console', true, true)) {
            $configs = $this->config->item('console/console');
        }

        if ($this->load->config('console', true, true)) {
            $configs = array_merge($configs, $this->config->item('console'));
        }

        $this->cli = new Cli($configs);
    }

    final public function index()
    {
        $args = func_get_args();

        if (!empty($this->available_commands)) {
            $this->cli->add_commands($this->available_commands);
        }

        return $this->cli->execute($args);
    }
}
