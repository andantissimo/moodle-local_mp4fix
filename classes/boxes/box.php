<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\boxes;

use local_mp4fix\io\stream;

interface box {
    public function get_size(): int;
    public function get_type(): string;
    public function parse(stream $payload, box_parser $parser);
    public function copy_to(stream $dest);
}
