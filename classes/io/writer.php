<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\io;

class writer {
    /** @var stream */
    protected $stream;

    public function __construct(stream $stream) {
        $this->stream = $stream;
    }

    public function write_4cc(string $value) {
        $this->stream->write(pack('a4', $value));
    }

    public function write_uint8(int $value) {
        $this->stream->write(pack('C', $value));
    }

    public function write_uint16(int $value) {
        $this->stream->write(pack('n', $value));
    }

    public function write_uint32(int $value) {
        $this->stream->write(pack('N', $value));
    }

    public function write_uint64(int $value) {
        $this->stream->write(pack('J', $value));
    }

    public function write_int8(int $value) {
        $this->stream->write(pack('C', $value));
    }

    public function write_int16(int $value) {
        $this->stream->write(pack('n', $value));
    }

    public function write_int32(int $value) {
        $this->stream->write(pack('N', $value));
    }

    public function write_int64(int $value) {
        $this->stream->write(pack('J', $value));
    }

    public function write_float16(float $value) {
        $this->stream->write(pack('n', round($value * 0x100)));
    }

    public function write_float32(float $value) {
        $this->stream->write(pack('N', round($value * 0x10000)));
    }

    /** @param \DateTime|null $value */
    public function write_time32($value) {
        $timestamp = $value ? self::datetime_to_timestamp($value) : 0;
        $this->stream->write(pack('N', $timestamp));
    }

    /** @param \DateTime|null $value */
    public function write_time64($value) {
        $timestamp = $value ? self::datetime_to_timestamp($value) : 0;
        $this->stream->write(pack('J', $timestamp));
    }

    /** @param int[] $values */
    public function write_uint8array(array $values) {
        $this->stream->write(pack('C*', ...$values));
    }

    /** @param int[] $values */
    public function write_uint16array(array $values) {
        $this->stream->write(pack('n*', ...$values));
    }

    /** @param int[] $values */
    public function write_uint32array(array $values) {
        $this->stream->write(pack('N*', ...$values));
    }

    /** @param int[] $values */
    public function write_uint64array(array $values) {
        $this->stream->write(pack('J*', ...$values));
    }

    public function write_uuid(string $value) {
        $this->stream->write(pack('H32', strtr($value, [ '-' => '' ])));
    }

    protected static function datetime_to_timestamp(\DateTime $date): int {
        /* @var $immutable \DateTimeImmutable */
        $immutable = \DateTimeImmutable::createFromMutable($date);
        return $immutable->add(new \DateInterval('P66Y1D'))->getTimestamp();
    }
}
