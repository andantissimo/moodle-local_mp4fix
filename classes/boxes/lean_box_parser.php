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
            $large = $source->read(8);
            if (strlen($large) !== 8)
                return null;
            $size = unpack('J', $large)[1];
            $payload = new substream($source, $source->tell(), $size - 16);
        } else {
            $payload = new substream($source, $source->tell(), $size - 8);
        }
        $box->parse($payload, $this);
        $payload->seek(0, SEEK_END);
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
