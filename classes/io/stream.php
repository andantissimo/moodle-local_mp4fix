<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\io;

interface stream {
    public function read(int $length): string;
    public function write(string $string);
    public function seek(int $offset, int $whence = SEEK_SET);
    public function tell(): int;
    public function get_size(): int;
    public function copy_to(stream $dest, int $maxlength = -1);
}
