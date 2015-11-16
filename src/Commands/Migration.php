<?php
namespace Projek\CI\Console\Commands;

use Projek\CI\Console;

class Migration extends Console\Commands
{
    protected $name = 'migration';
    protected $description = 'Manage migation database';

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
            ],
            'list' => [
                'prefix' => 'l',
                'longPrefix' => 'list',
                'description' => 'Display All migraiton list',
                'noValue' => true
            ],
            'current' => [
                'prefix' => 'c',
                'longPrefix' => 'current',
                'description' => 'Display current migraiton version',
                'noValue' => true
            ],
            'to' => [
                'prefix' => 't',
                'longPrefix' => 'to',
                'description' => 'Migrate to certain version. '
                    .'See --list for available versions.',
                'castTo' => 'int'
            ]
        ]);
    }

    /**
     * {inheridoc}
     */
    public function execute(Console $console)
    {
        $this->CI->load->library('migration');
        $arguments = $console->argumentManager();

        if ($arguments->defined('list')) {
            $this->get_current($console);
            if ($table = $this->get_list()) {
                return $console->table($table);
            }

            return $console->dump($this->get_list());
        }

        if ($arguments->defined('current')) {
            return $this->get_current($console);
        }

        if ($arguments->defined('to')) {
            $version = $arguments->get('to');

            return $this->jump_to($version, $console);
        }

        return false;
    }

    protected function get_list()
    {
        $table = [];
        $migrations = $this->CI->migration->find_migrations();

        foreach ($migrations as $version => $file) {
            $file = explode('_', basename($file, '.php'));
            array_shift($file);
            $table[] = [
                'Version' => $version,
                'File name' => implode(' ', $file),
            ];
        }

        return $table;
    }

    protected function get_current($console)
    {
        $current = $this->CI->migration->get_version();

        if ($this->is_latest()) {
            $console->out('<green>You are already in latest version</green>');
            return $console->out('which is: <green>'.$current.'</green>');
        }

        $console->out('Your have installed version: <green>'.$current.'</green>');
        return $console->out('Use --help for more information');
    }

    protected function jump_to($version = 0, $console)
    {
        if ($this->is_latest()) {
            return $console->out('<green>You are already in latest version</green>');;
        }

        if (!$version) {
            $version = $this->CI->migration->latest();
        }

        $this->CI->migration->version($version);

        return $console->out('<green>Done!</green> migrated to version '.$version);
    }

    protected function is_latest()
    {
        $current = $this->CI->migration->get_version();
        $latest  = $this->CI->migration->get_count();

        return ($current == $latest);
    }
}
