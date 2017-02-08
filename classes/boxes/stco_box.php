<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\boxes;

use local_mp4fix\io\{stream, reader, writer};

class stco_box implements full_box {
    use full_box_trait;

    /** @var string */
    protected $type = 'stco';

    /** @var int[] */
    public $chunk_offsets = [];

    public function __construct(string $type = 'stco') {
        $this->type = $type;
    }

    public function get_size(): int {
        $size = 12; // size, type, version, flags
        $size += 4; // entry_count
        $entry_count = count($this->chunk_offsets);
        if ($this->type === 'co64') {
            $size += $entry_count * 8; // chunk_offset
        } else {
            $size += $entry_count * 4; // chunk_offset
        }
        return $size;
    }

    public function get_type(): string {
        return $this->type;
    }

    public function parse(stream $payload, box_parser $factory) {
        $this->parse_full_box_header($payload);
        $reader = new reader($payload);
        $entry_count = $reader->read_uint32();
        if ($this->type === 'co64') {
            $this->chunk_offsets = $reader->read_uint64array($entry_count);
        } else {
            $this->chunk_offsets = $reader->read_uint32array($entry_count);
        }
    }

    public function copy_to(stream $dest) {
        $this->copy_full_box_header_to($dest);
        $writer = new writer($dest);
        $writer->write_uint32(count($this->chunk_offsets));
        if ($this->type === 'co64') {
            $writer->write_uint64array($this->chunk_offsets);
        } else {
            $writer->write_uint32array($this->chunk_offsets);
        }
    }
}
