<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix;

use local_mp4fix\boxes\{box, ftyp_box, moov_box, mdat_box, lean_box_parser};
use local_mp4fix\invalid_format_exception;
use local_mp4fix\io\stream;

/**
 * @property-read bool $is_progressive
 */
class movie implements \IteratorAggregate {
    /** @var box[] */
    protected $boxes = [];

    public function __get($name) {
        switch ($name) {
        case 'is_progressive':
            foreach ($this->boxes as $box) {
                if ($box instanceof moov_box)
                    return true;
                if ($box instanceof mdat_box)
                    return false;
            }
            throw new invalid_format_exception;
        }
        throw new \invalid_parameter_exception;
    }

    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->boxes);
    }

    public function get_ftyp_box(): ftyp_box {
        foreach ($this->boxes as $box) {
            if ($box instanceof ftyp_box)
                return $box;
        }
        throw new invalid_format_exception;
    }

    public function get_moov_box(): moov_box {
        foreach ($this->boxes as $box) {
            if ($box instanceof moov_box)
                return $box;
        }
        throw new invalid_format_exception;
    }

    public function add(box $box) {
        $this->boxes[] = $box;
    }

    public function adjust_offsets(int $delta) {
        foreach ($this->get_moov_box()->get_trak_boxes() as $trak) {
            $stco = $trak->get_mdia_box()
                         ->get_minf_box()
                         ->get_stbl_box()
                         ->get_stco_box();
            foreach ($stco->chunk_offsets as &$offset)
                $offset += $delta;
            unset($offset);
        }
    }

    public function copy_to(stream $dest) {
        foreach ($this->boxes as $box)
            $box->copy_to($dest);
    }

    public static function parse(stream $source): movie {
        $parser = new lean_box_parser;
        $movie = new self;
        while ($box = $parser->parse($source))
            $movie->boxes[] = $box;
        return $movie;
    }
}
