<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

$observers = array(
    [
        'eventname' => '\core\event\course_module_created',
        'callback'  => '\local_mp4fix\observer::mod_created',
        'internal'  => false,
        'priority'  => 1000,
    ],
    [
        'eventname' => '\core\event\course_module_updated',
        'callback'  => '\local_mp4fix\observer::mod_updated',
        'internal'  => false,
        'priority'  => 1000,
    ],
);
