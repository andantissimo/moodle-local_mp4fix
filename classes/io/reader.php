<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\io;

class reader {
    /** @var stream */
    protected $stream;

    public function __construct(stream $stream) {
        $this->stream = $stream;
    }

    public function read_all(): string {
        return $this->stream->read($this->stream->get_size() - $this->stream->tell());
    }

    public function read_4cc(): string {
        return $this->stream->read(4);
    }

    public function read_uint8(): int {
        return unpack('C', $this->stream->read(1))[1];
    }

    public function read_uint16(): int {
        return unpack('n', $this->stream->read(2))[1];
    }

    public function read_uint32(): int {
        return unpack('N', $this->stream->read(4))[1];
    }

    public function read_uint64(): int {
        $unsigned = unpack('J', $this->stream->read(8))[1];
        if ($unsigned < 0)
            throw new \OverflowException;
        return $unsigned;
    }

    public function read_int8(): int {
        $unsigned = unpack('C', $this->stream->read(1))[1];
        return $unsigned - (($unsigned & 0x80) << 1);
    }

    public function read_int16(): int {
        $unsigned = unpack('n', $this->stream->read(2))[1];
        return $unsigned - (($unsigned & 0x8000) << 1);
    }

    public function read_int32(): int {
        $unsigned = unpack('N', $this->stream->read(4))[1];
        return $unsigned - (($unsigned & 0x80000000) << 1);
    }

    public function read_int64(): int {
        return unpack('J', $this->stream->read(8))[1];
    }

    public function read_float16(): float {
        return unpack('n', $this->stream->read(2))[1] / 0x100;
    }

    public function read_float32(): float {
        return unpack('N', $this->stream->read(4))[1] / 0x10000;
    }

    /** @return \DateTime|null */
    public function read_time32() {
        $timestamp = unpack('N', $this->stream->read(4))[1];
        return $timestamp ? self::timestamp_to_datetime($timestamp) : null;
    }

    /** @return \DateTime|null */
    public function read_time64() {
        $timestamp = unpack('J', $this->stream->read(8))[1];
        return $timestamp ? self::timestamp_to_datetime($timestamp) : null;
    }

    /** @return int[] */
    public function read_uint8array(int $count): array {
        return array_merge(unpack('C*', $this->stream->read(1 * $count)));
    }

    /** @return int[] */
    public function read_uint16array(int $count): array {
        return array_merge(unpack('n*', $this->stream->read(2 * $count)));
    }

    /** @return int[] */
    public function read_uint32array(int $count): array {
        return array_merge(unpack('N*', $this->stream->read(4 * $count)));
    }

    /** @return int[] */
    public function read_uint64array(int $count): array {
        return array_merge(unpack('J*', $this->stream->read(8 * $count)));
    }

    public function read_uuid(): string {
        $x = unpack('H32', $this->stream->read(16))[1];
        return substr($x, 0, 8).'-'.substr($x, 8, 4).'-'.substr($x, 12, 4).'-'.substr($x, 16, 4).'-'.substr($x, 20);
    }

    protected static function timestamp_to_datetime(int $timestamp): \DateTime {
        $date = new \DateTime(null, new \DateTimeZone('UTC'));
        $date->setTimestamp($timestamp);
        $date->sub(new \DateInterval('P66Y1D'));
        return $date;
    }
}
