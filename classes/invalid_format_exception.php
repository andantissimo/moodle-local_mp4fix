<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix;

class invalid_format_exception extends \moodle_exception {
    /**
     * Constructor
     * @param string $debuginfo some detailed information
     */
    function __construct($debuginfo = null) {
        parent::__construct('invalidformat', 'local_mp4fix', '', null, $debuginfo);
    }
}
