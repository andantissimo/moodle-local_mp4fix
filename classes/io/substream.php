<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\io;

class substream implements stream {
    /** @var stream */
    protected $stream;
    /** @var int */
    protected $offset;
    /** @var int */
    protected $length;

    public function __construct(stream $stream, int $offset, int $length) {
        $this->stream = $stream;
        $this->offset = $offset;
        $this->length = $length;
    }

    public function read(int $length): string {
        $length = min($this->length - $this->tell(), $length);
        return $this->stream->read($length);
    }

    public function write(string $string) {
        $this->stream->write($string);
    }

    public function seek(int $offset, int $whence = SEEK_SET) {
        switch ($whence) {
        case SEEK_CUR:
            $offset += $this->tell();
            break;
        case SEEK_END:
            $offset += $this->length;
            break;
        }
        if ($offset < 0 || $this->length < $offset)
            throw new \RangeException;
        $this->stream->seek($this->offset + $offset);
    }

    public function tell(): int {
        return $this->stream->tell() - $this->offset;
    }

    public function get_size(): int {
        return $this->length;
    }

    public function copy_to(stream $dest, int $maxlength = -1) {
        $limit = $this->length - $this->tell();
        $maxlength = $maxlength < 0 ? $limit : min($limit, $maxlength);
        $this->stream->copy_to($dest, $maxlength);
    }
}
