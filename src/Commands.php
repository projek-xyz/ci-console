<?php
namespace Projek\CI\Console;

abstract class Commands
{
    /**
     * Command name
     *
     * @var string
     */
    protected $name = null;

    /**
     * Command description
     *
     * @var string
     */
    protected $description = null;

    /**
     * Codeigniter instance
     *
     * @var mixed
     */
    protected $CI;

    public function __construct($ci)
    {
        $this->CI =& $ci;
    }

    /**
     * Get command name
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get command description
     *
     * @return string
     */
    public function description()
    {
        if (substr($this->description, 0, 5) == 'lang:') {
            $description = substr($this->description, 5);

            if ($desc = $this->CI->lang->line($description)) {
                return $desc;
            }

            return $description;
        }

        return $this->description;
    }

    /**
     * Register arguments
     *
     * @param Projek\CI\Console\Arguments\Manager
     */
    abstract protected function register(Arguments\Manager $arguments);

    /**
     * Execute command
     *
     * @param Projek\CI\Console\Cli $console
     */
    abstract protected function execute(Cli $console);

    /**
     * Initialize commands
     *
     * @param  array                 $args    Arguments
     * @param  Projek\CI\Console\Cli $console
     * @return mixed
     */
    public function initialize($args, Cli $console)
    {
        $arguments = $console->argument_manager();

        $arguments->description($this->description());
        $this->register($arguments);

        try {
            $arguments->parse($args);
            $executed = $this->execute($console);
        } catch (\Exception $e) {
            $executed = false;
            return EXIT_USER_INPUT;
        }

        if (!$executed or $arguments->defined('help')) {
            return $console->usage($args, $this->name);
        }
    }
}
