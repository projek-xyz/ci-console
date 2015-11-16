<?php
namespace Projek\CI\Console;

use League\CLImate\CLImate;
use Projek\CI\Console\Commands;
use Projek\CI\Console\ArgumentManager;
use Projek\CI\Console\Extensions\TabExtension;

class Cli
{
    /**
     * CLImate instance
     *
     * @var League\CLImate\CLImate
     */
    protected $climate;

    /**
     * All available commands
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Configuration
     *
     * @var array
     */
    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->climate = new CLImate();

        $this->climate->addArt(__DIR__ . '/Console/arts');
        $this->climate->setArgumentManager(new Arguments\Manager());
        $this->climate->extend(Extensions\Tab::class, 'tab');

        $this->climate->arguments->description('Yet another Codeigniter Starter Application');
        $this->climate->arguments->add([
            'help' => [
                'prefix' => 'h',
                'longPrefix' => 'help',
                'description' => lang('console_display_help'),
                'noValue' => true
            ]
        ]);

        // $this->addCommands([
        //     Commands\Install::class,
        //     Commands\Migration::class,
        // ]);
    }

    /**
     * Get CLImate instance
     *
     * @return League\CLImate\CLImate
     */
    public function climate()
    {
        return $this->climate;
    }

    /**
     * Register multiple commands
     *
     * @param array $commands List of commands
     */
    public function add_commands(array $commands)
    {
        foreach ($commands as $command) {
            $this->add_command($command);
        }
    }

    /**
     * Register new command
     *
     * @param string|Projek\CI\Console\Commands $command Command instances
     */
    public function add_command($command)
    {
        if (is_string($command)) {
            $ci =& get_instance();
            $command = (new \ReflectionClass($command))->newInstance($ci);
        }

        if (!$command instanceof Commands) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Argument 1 passed to %s must be an instance of %s, %s given',
                    __NAMESPACE__ . '\Console::add_command()',
                    __NAMESPACE__ . '\Console\Commands',
                    gettype($command)
                )
            );
        }

        $this->commands[$command->name()] = $command;
    }

    /**
     * Remove registered command
     *
     * @param string $command Command key
     */
    public function remove_command($command)
    {
        if (isset($this->commands[$command])) {
            unset($this->commands[$command]);
        }
    }

    /**
     * Retrieve all available commands
     *
     * @return array
     */
    public function commands()
    {
        return $this->commands;
    }

    /**
     * Execute command
     *
     * @param  array $argv Arguments
     * @return mixed
     */
    public function execute(array $argv = [])
    {
        if (empty($argv)) {
            return $this->help();
        }

        $cmd = array_shift($argv);
        if (isset($this->commands[$cmd])) {
            return $this->commands[$cmd]->initialize($argv, $this);
        }

        return $this->help($argv);
    }

    /**
     * Print usage
     *
     * @param  array $args Arguments
     * @return mixed
     */
    public function help(array $args = [])
    {
        $this->climate->draw('creasi-logo');

        $this->usage($args);

        $this->climate->br()->out(
            sprintf('<yellow>%s</yellow>:', lang('console_commands_available'))
        );

        $len = [];

        foreach ($this->commands as $name => $cmd) {
            $len[] = strlen($cmd->name());
        }

        foreach ($this->commands as $name => $cmd) {
            $spc = max($len) + 2 - strlen($name);
            $this->climate->tab()->out(
                '<green>' . $cmd->name() . '</green>' . str_repeat(' ', $spc) . $cmd->description()
            );
        }

        return (int) $args === null;
    }

    /**
     * Draw Creasi.co Banner
     *
     * @return Projek\CI\Console
     */
    public function usage(array $args = [], $command = '')
    {
        if (empty($args)) {
            $command or $command = '[command]';
            array_unshift($args, './creasi '.$command);
        }

        return $this->climate->usage($args);
    }

    /**
     * Toggle ANSI support on or off
     *
     * @param  bool $enable Switcer on or off
     * @return Projek\CI\Console
     */
    public function force_ansi($enable = true)
    {
        if ($enable) {
            $this->climate->forceAnsiOn();
        } else {
            $this->climate->forceAnsiOff();
        }

        return $this;
    }

    /**
     * Returns Argument manager
     *
     * @return League\CLImate\Argument\Manager
     */
    public function argument_manager()
    {
        return $this->climate->arguments;
    }

    /**
     * CLImate output preset
     */

    /**
     * Returns CLImate comment output
     *
     * @param  string $string Output
     * @return mixed
     */
    public function comment($string)
    {
        return $this->climate->comment($string);
    }

    /**
     * Returns CLImate whisper output
     *
     * @param  string $string Output
     * @return mixed
     */
    public function whisper($string)
    {
        return $this->climate->whisper($string);
    }

    /**
     * Returns CLImate shout output
     *
     * @param  string $string Output
     * @return mixed
     */
    public function shout($string)
    {
        return $this->climate->shout($string);
    }

    /**
     * Returns CLImate error output
     *
     * @param  string $string Output
     * @return mixed
     */
    public function error($string)
    {
        return $this->climate->error($string);
    }

    /**
     * CLImate base output
     */

    /**
     * Returns CLImate output
     *
     * @param  string $string Output
     * @return mixed
     */
    public function out($string)
    {
        return $this->climate->out($string);
    }

    /**
     * Returns CLImate inline text
     *
     * @param  string $string Output
     * @return mixed
     */
    public function inline($string)
    {
        return $this->climate->inline($string);
    }

    /**
     * Returns CLImate draw art
     * @see http://climate.thephpleague.com/terminal-objects/draw/
     *
     * @param  string $string Output
     * @return mixed
     */
    public function draw($string)
    {
        return $this->climate->draw($string);
    }

    /**
     * Returns CLImate json
     * @see http://climate.thephpleague.com/terminal-objects/json/
     *
     * @param  mixed $mixed String|Array|Object
     * @return mixed
     */
    public function json($mixed)
    {
        return $this->climate->json($mixed);
    }

    /**
     * Returns CLImate table
     * @see http://climate.thephpleague.com/terminal-objects/table/
     *
     * @param  array $array Table data
     * @return mixed
     */
    public function table(array $array)
    {
        return $this->climate->table($array);
    }

    /**
     * Draw a border
     * @see http://climate.thephpleague.com/terminal-objects/border/
     *
     * @param  string $char   Border character
     * @param  int    $length Border length
     * @return mixed
     */
    public function border($char = null, $length = null)
    {
        return $this->climate->border($char, $length);
    }

    /**
     * Draw padding
     * @see http://climate.thephpleague.com/terminal-objects/padding/
     *
     * @param  int    $length Padding length
     * @param  string $char   Padding character
     * @return mixed
     */
    public function padding($length = 0, $char = '.')
    {
        return $this->climate->padding($length, $char);
    }

    /**
     * Returns output in columns
     * @see http://climate.thephpleague.com/terminal-objects/columns/
     *
     * @param  array $data         Output data
     * @param  int   $column_count Number of columns
     * @return mixed
     */
    public function columns(array $data, $column_count = null)
    {
        return $this->climate->columns($data, $column_count);
    }

    /**
     * Pay attantion to this output
     * @see http://climate.thephpleague.com/terminal-objects/flank/
     *
     * @param  string $output Output string
     * @param  string $char   Special character
     * @param  int    $length Character length
     * @return mixed
     */
    public function flank($output, $char = null, $length = null)
    {
        return $this->climate->flank($output, $char, $length);
    }

    /**
     * Create a progressbar
     * @see http://climate.thephpleague.com/terminal-objects/progress-bar/
     *
     * @param  int   $total Total progress
     * @return mixed
     */
    public function progress($total = null)
    {
        return $this->climate->progress($total);
    }

    /**
     * CLImate inputs
     */

    /**
     * Wanna ask something
     *
     * @param  string         $prompt     The question you want to ask for
     * @param  string         $default    Default answer
     * @param  array|callable $acceptable Acceptable answer
     * @param  bool           $strict     Case-sensitife?
     * @return string
     */
    public function input($prompt, $default = '', $acceptable = null, $strict = false)
    {
        $input = $this->climate->input($prompt);

        if (! empty($default)) {
            $input->defaultTo($default);
        }

        if (null !== $acceptable) {
            $input->accept($acceptable, true);
        }

        if (true === $strict) {
            $input->strict();
        }

        return $input->prompt();
    }

    /**
     * Ask something secretly?
     *
     * @param  string $prompt The question you want to ask for
     * @return string
     */
    public function password($prompt)
    {
        $password = $this->climate->password($prompt);

        return $password->prompt();
    }

    /**
     * Choise between yes or no?
     *
     * @param  string $prompt The question you want to ask for
     * @return bool
     */
    public function confirm($prompt)
    {
        $confirm = $this->climate->confirm($prompt);

        return $confirm->confirmed();
    }

    /**
     * Choise multiple answer from given options?
     *
     * @param  string $prompt  The question you want to ask for
     * @param  array  $options Available options
     * @return string
     */
    public function checkboxes($prompt, array $options)
    {
        $checkboxes = $this->climate->checkboxes($prompt, $options);

        return $checkboxes->prompt();
    }

    /**
     * Choise an answer from given options?
     *
     * @param  string $prompt  The question you want to ask for
     * @param  array  $options Available options
     * @return string
     */
    public function radio($prompt, array $options)
    {
        $radio = $this->climate->radio($prompt, $options);

        return $radio->prompt();
    }

    /**
     * Dumb any data
     * @see http://climate.thephpleague.com/terminal-objects/dump/
     *
     * @param  mixed $array Data to dump
     * @return mixed
     */
    public function dump($array)
    {
        return $this->climate->dump($array);
    }

    /**
     * Returns CLImate new line
     * @see http://climate.thephpleague.com/terminal-objects/br/
     *
     * @param  int $count Number of new line
     * @return mixed
     */
    public function br($count = 1)
    {
        return $this->climate->br($count);
    }

    /**
     * Returns CLImate new tab
     * @see http://climate.thephpleague.com/terminal-objects/tab/
     *
     * @param  int $count Number of new tab
     * @return mixed
     */
    public function tab($count = 1)
    {
        return $this->climate->tab($count);
    }

    /**
     * Returns CLImate clear output
     * @see http://climate.thephpleague.com/terminal-objects/clear/
     *
     * @return mixed
     */
    public function clear()
    {
        return $this->climate->clear();
    }
}
