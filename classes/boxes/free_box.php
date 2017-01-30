<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\boxes;

class free_box implements box {
    use free_box_trait;

    /** @var string */
    protected $type;

    public function __construct(string $type) {
        $this->type = $type;
    }
}
