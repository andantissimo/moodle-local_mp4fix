<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\io;

class fstream implements stream {
    /** @var resource */
    protected $handle;

    public static function open(string $filename): fstream {
        return new self(fopen($filename, 'rb'));
    }

    public static function create(string $filename): fstream {
        return new self(fopen($filename, 'wb'));
    }

    /** @param resource $handle */
    public static function attach($handle): fstream {
        return new self($handle);
    }

    /** @param resource $handle */
    protected function __construct($handle) {
        if (!$handle)
            throw new \invalid_parameter_exception;
        $this->handle = $handle;
    }

    public function read(int $length): string {
        if ($length <= 0)
            return '';
        return fread($this->handle, $length);
    }

    public function write(string $string) {
        fwrite($this->handle, $string);
    }

    public function seek(int $offset, int $whence = SEEK_SET) {
        fseek($this->handle, $offset, $whence);
    }

    public function tell(): int {
        return ftell($this->handle);
    }

    public function get_size(): int {
        $stat = fstat($this->handle);
        return $stat['size'];
    }

    public function copy_to(stream $dest, int $maxlength = -1) {
        if ($dest instanceof fstream)
            stream_copy_to_stream($this->handle, $dest->handle, $maxlength);
        else
            $dest->write(stream_get_contents($this->handle, $maxlength));
    }

    public function __destruct() {
        fclose($this->handle);
    }
}
