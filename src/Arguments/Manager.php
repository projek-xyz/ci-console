<?php
namespace Projek\CI\Console\Arguments;

use League\CLImate\Argument\Manager as BaseManager;

class Manager extends BaseManager
{
    public function __construct()
    {
        parent::__construct();

        $this->summary = new Summary();
    }
}
