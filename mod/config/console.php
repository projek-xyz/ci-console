<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * All of these settings could be overwrited from application config
 */

/**
 * Basic art directory
 */
$config['art_directory'] = realpath(__DIR__.'/../../asset/arts');

/**
 * Registering available commands
 */
$config['available_commands'] = [];

/**
 * Directory contains all of application console commands
 * TODO: implement this feature
 */
// $config['command_directories'] = '';
