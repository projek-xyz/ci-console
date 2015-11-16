<?php
namespace Projek\CI\Console;

trait ExceptionsTrait
{
    private $views;
    private $_template_path;

    private function render_cli_error($heading, array $messages, $traces = [])
    {
        $cli = new Cli;

        $cli->error(sprintf('<underline><bold>%s</bold></underline>', $heading));

        foreach ($messages as $label => $message) {
            $cli->error(sprintf('%s : <bold>%s</bold>', $label, $message));
        }

        if ($traces) {
            $cli->br()->error('<underline><bold>Backtrace</bold></underline>');

            $i = 1;
            foreach ($traces as $error) {
                $line = isset($error['line']) ? $error['line'] : 'Unknown';
                if (isset($error['file'])) {
                    $console->out(' '.$i.') ' . str_replace(FCPATH, './', $error['file']) . ':' . $line);
                } else {
                    $i--;
                }
                $func = '';
                if (isset($error['class'], $error['type'])) {
                    $func .= $error['class'].$error['type'];
                }
                $func .= $error['function'];
                $console->error('    ' . $func . '()');
                $i++;
            }
        }
    }
}
