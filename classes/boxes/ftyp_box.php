<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\boxes;

use local_mp4fix\io\{stream, reader, writer};

class ftyp_box implements box {
    use box_trait;

    /** @var string */
    protected $type = 'ftyp';

    /** @var string */
    public $major_brand       = 'isom';
    /** @var int */
    public $minor_version     = 0;
    /** @var string[] */
    public $compatible_brands = [];

    public function get_size(): int {
        $size  = 8; // size, type
        $size += 4; // major_brand
        $size += 4; // minor_version
        $size += 4 * count($this->compatible_brands);
        return $size;
    }

    public function get_type(): string {
        return $this->type;
    }

    public function parse(stream $payload, box_parser $factory) {
        $reader = new reader($payload);
        $this->major_brand   = $reader->read_4cc();
        $this->minor_version = $reader->read_uint32();
        while (strlen($brand = $reader->read_4cc()) === 4)
            $this->compatible_brands[] = $brand;
    }

    public function copy_to(stream $dest) {
        $this->copy_box_header_to($dest);
        $writer = new writer($dest);
        $writer->write_4cc($this->major_brand);
        $writer->write_uint32($this->minor_version);
        foreach ($this->compatible_brands as $brand)
            $writer->write_4cc($brand);
    }
}
