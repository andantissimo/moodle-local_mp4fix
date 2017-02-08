<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\boxes;

use local_mp4fix\io\{stream, substream};

class lean_box_parser implements box_parser {
    /**
     * @param stream $source
     * @return box|null
     */
    public function parse(stream $source) {
        $header = $source->read(8);
        if (strlen($header) !== 8)
            return null;
        $size = unpack('N', substr($header, 0, 4))[1];
        $type = substr($header, 4, 4);
        $box = self::create_box($type);
        if ($size === 1) {
            $header .= $source->read(8);
            if (strlen($header) !== 16)
                return null;
            $size = unpack('J', substr($header, 8, 8))[1];
        }
        $offset = $source->tell();
        $payload = new substream($source, $offset, $size - strlen($header));
        $box->parse($payload, $this);
        $source->seek($offset + $payload->get_size());
        return $box;
    }

    protected static function create_box(string $type): box {
        switch ($type) {
        case 'ftyp': return new ftyp_box;
        case 'moov': return new moov_box;
        case 'mvhd': return new mvhd_box;
        case 'trak': return new trak_box;
        case 'tkhd': return new tkhd_box;
        case 'mdia': return new mdia_box;
        case 'minf': return new minf_box;
        case 'stbl': return new stbl_box;
        case 'stco': case 'co64': return new stco_box($type);
        case 'mdat': return new mdat_box;
        }
        return new free_box($type);
    }
}
