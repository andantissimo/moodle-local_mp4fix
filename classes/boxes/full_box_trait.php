<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\boxes;

use local_mp4fix\io\stream;

trait full_box_trait {
    use box_trait;

    /** @var int */
    protected $version;
    /** @var int */
    protected $flags;

    public function get_version(): int {
        return $this->version;
    }

    public function get_flags(): int {
        return $this->flags;
    }

    protected function parse_full_box_header(stream $payload) {
        $bitfield = unpack('N', $payload->read(4))[1];
        $this->version = $bitfield >> 24 & 0xFF;
        $this->flags   = $bitfield & 0x00FFFFFF;
    }

    protected function copy_full_box_header_to(stream $dest) {
        $this->copy_box_header_to($dest);
        $dest->write(pack('N', $this->version << 24 | $this->flags));
    }
}
