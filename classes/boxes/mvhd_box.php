<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\boxes;

use local_mp4fix\io\{stream, reader, writer};

class mvhd_box implements full_box {
    use full_box_trait;

    /** @var string */
    protected $type = 'mvhd';

    /** @var \DateTime|null */
    public $creation_time     = null;
    /** @var \DateTime|null */
    public $modification_time = null;
    /** @var int */
    public $timescale         = 1000;
    /** @var int */
    public $duration          = 0;
    /** @var float */
    public $rate              = 1.0;
    /** @var float */
    public $volume            = 1.0;
    /** @var int[9] */
    public $matrix            = [ 0x00010000, 0, 0, 0, 0x00010000, 0, 0, 0, 0x40000000 ];
    /** @var int */
    public $next_track_id     = -1;

    public function get_size(): int {
        $size = 12; // size, type, version, flags
        if ($this->version === 0) {
            $size += 4; // creation_time
            $size += 4; // modification_time
            $size += 4; // timescale
            $size += 4; // duration
        } else {
            $size += 8; // creation_time
            $size += 8; // modification_time
            $size += 4; // timescale
            $size += 8; // duration
        }
        $size += 4;     // rate
        $size += 2;     // volume
        $size += 2;     // reserved
        $size += 4 * 2; // reserved
        $size += 4 * 9; // matrix
        $size += 4 * 6; // pre_defined
        $size += 4;     // next_track_ID
        return $size;
    }

    public function get_type(): string {
        return $this->type;
    }

    public function parse(stream $payload, box_parser $factory) {
        $this->parse_full_box_header($payload);
        $reader = new reader($payload);
        if ($this->version === 0) {
            $this->creation_time     = $reader->read_time32();
            $this->modification_time = $reader->read_time32();
            $this->timescale         = $reader->read_uint32();
            $this->duration          = $reader->read_uint32();
        } else {
            $this->creation_time     = $reader->read_time64();
            $this->modification_time = $reader->read_time64();
            $this->timescale         = $reader->read_uint32();
            $this->duration          = $reader->read_uint64();
        }
        $this->rate          = $reader->read_float32();
        $this->volume        = $reader->read_float16();
        $payload->read(2);
        $payload->read(4 * 2);
        $this->matrix        = $reader->read_uint32array(9);
        $payload->read(4 * 6);
        $this->next_track_id = $reader->read_uint32();
    }

    public function copy_to(stream $dest) {
        $this->copy_box_header_to($dest);
        $this->copy_full_box_header_to($dest);
        $writer = new writer($dest);
        if ($this->version === 0) {
            $writer->write_time32($this->creation_time);
            $writer->write_time32($this->modification_time);
            $writer->write_uint32($this->timescale);
            $writer->write_uint32($this->duration);
        } else {
            $writer->write_time64($this->creation_time);
            $writer->write_time64($this->modification_time);
            $writer->write_uint32($this->timescale);
            $writer->write_uint64($this->duration);
        }
        $writer->write_float32($this->rate);
        $writer->write_float16($this->volume);
        $writer->write_uint16(0);
        $writer->write_uint32array([ 0, 0 ]);
        $writer->write_uint32array($this->matrix);
        $writer->write_uint32array([ 0, 0, 0, 0, 0, 0 ]);
        $writer->write_uint32($this->next_track_id);
    }
}
