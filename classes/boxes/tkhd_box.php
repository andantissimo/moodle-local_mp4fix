<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\boxes;

use local_mp4fix\io\{stream, reader, writer};

class tkhd_box implements full_box {
    use full_box_trait;

    /** @var string */
    protected $type = 'tkhd';

    /** @var \DateTime|null */
    public $creation_time     = null;
    /** @var \DateTime|null */
    public $modification_time = null;
    /** @var int */
    public $track_id          = 0;
    /** @var int */
    public $duration          = 0;
    /** @var int */
    public $layer             = 0;
    /** @var int */
    public $alternate_group   = 0;
    /** @var float */
    public $volume            = 0.0;
    /** @var int[9] */
    public $matrix            = [ 0x00010000, 0, 0, 0, 0x00010000, 0, 0, 0, 0x40000000 ];
    /** @var float */
    public $width             = 0.0;
    /** @var float */
    public $height            = 0.0;

    public function get_size(): int {
        $size = 12; // size, type, version, flags
        if ($this->version === 0) {
            $size += 4; // creation_time
            $size += 4; // modification_time
            $size += 4; // track_ID
            $size += 4; // reserved
            $size += 4; // duration
        } else {
            $size += 8; // creation_time
            $size += 8; // modification_time
            $size += 4; // track_ID
            $size += 4; // reserved
            $size += 8; // duration
        }
        $size += 4 * 2; // reserved
        $size += 2;     // layer
        $size += 2;     // alternate_group
        $size += 2;     // volume
        $size += 2;     // reserved
        $size += 4 * 9; // matrix
        $size += 4;     // width
        $size += 4;     // height
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
            $this->track_id          = $reader->read_uint32();
            $payload->read(4);
            $this->duration          = $reader->read_uint32();
        } else {
            $this->creation_time     = $reader->read_time64();
            $this->modification_time = $reader->read_time64();
            $this->track_id          = $reader->read_uint32();
            $payload->read(4);
            $this->duration          = $reader->read_uint64();
        }
        $payload->read(4 * 2);
        $this->layer           = $reader->read_uint16();
        $this->alternate_group = $reader->read_uint16();
        $this->volume          = $reader->read_float16();
        $payload->read(2);
        $this->matrix          = $reader->read_uint32array(9);
        $this->width           = $reader->read_float32();
        $this->height          = $reader->read_float32();
    }

    public function copy_to(stream $dest) {
        $this->copy_full_box_header_to($dest);
        $writer = new writer($dest);
        if ($this->version === 0) {
            $writer->write_time32($this->creation_time);
            $writer->write_time32($this->modification_time);
            $writer->write_uint32($this->track_id);
            $writer->write_uint32(0);
            $writer->write_uint32($this->duration);
        } else {
            $writer->write_time64($this->creation_time);
            $writer->write_time64($this->modification_time);
            $writer->write_uint32($this->track_id);
            $writer->write_uint32(0);
            $writer->write_uint64($this->duration);
        }
        $writer->write_uint32array([ 0, 0 ]);
        $writer->write_uint16($this->layer);
        $writer->write_uint16($this->alternate_group);
        $writer->write_float16($this->volume);
        $writer->write_uint16(0);
        $writer->write_uint32array($this->matrix);
        $writer->write_float32($this->width);
        $writer->write_float32($this->height);
    }
}
