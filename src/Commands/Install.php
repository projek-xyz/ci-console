<?php
namespace Projek\CI\Console\Commands;

use Projek\CI\Console;

class Install extends Console\Commands
{
    protected $name = 'install';
    protected $description = 'Run the installer';

    /**
     * {inheridoc}
     */
    public function register(Console\ArgumentManager $arguments)
    {
        $arguments->add([
            'help' => [
                'prefix' => 'h',
                'longPrefix' => 'help',
                'description' => 'Display this help',
                'noValue' => true
            ]
        ]);
    }

    /**
     * {inheridoc}
     */
    public function execute(Console $console)
    {
        $this->CI->load->library('migration');

        if ( ! $this->CI->migration->latest()) {
            show_error($this->CI->migration->error_string());
        } else {
            $console->greenBold('Congratulation! everything is ready to go');
        }

        return false;
    }
}
