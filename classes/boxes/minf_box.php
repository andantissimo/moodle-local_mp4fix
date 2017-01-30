<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\boxes;

use local_mp4fix\invalid_format_exception;

class minf_box implements container_box {
    use container_box_trait;

    /** @var string */
    protected $type = 'minf';

    public function get_stbl_box(): stbl_box {
        foreach ($this->children as $box) {
            if ($box instanceof stbl_box)
                return $box;
        }
        throw new invalid_format_exception;
    }
}
