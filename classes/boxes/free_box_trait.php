<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\boxes;

use local_mp4fix\io\stream;

trait free_box_trait {
    use box_trait;

    /** @var stream */
    protected $payload;

    public function get_size(): int {
        $size = 8 + $this->payload->get_size();
        if ($size > 0xFFFFFFFF)
            $size += 8;
        return $size;
    }

    public function get_type(): string {
        return $this->type;
    }

    public function parse(stream $payload, box_parser $parser) {
        $this->payload = $payload;
    }

    public function copy_to(stream $dest) {
        $this->copy_box_header_to($dest);
        $this->payload->seek(0);
        $this->payload->copy_to($dest);
    }
}
