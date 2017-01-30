<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\boxes;

use local_mp4fix\io\stream;

trait box_trait {
    protected function copy_box_header_to(stream $dest) {
        $size = $this->get_size();
        if ($size <= 0xFFFFFFFF)
            $dest->write(pack('Na4', $size, $this->get_type()));
        else
            $dest->write(pack('Na4J', 1, $this->get_type(), $size));
    }
}
