<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix\boxes;

use local_mp4fix\io\stream;

trait container_box_trait {
    use box_trait;

    /** @var box[] */
    protected $children = [];

    /** @return box[] */
    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->children);
    }

    public function add(box $box) {
        $this->children[] = $box;
    }

    public function remove(int $i) {
        array_splice($this->children, $i, 1);
    }

    public function get_size(): int {
        $size = 8;
        foreach ($this->children as $box)
            $size += $box->get_size();
        if ($size > 0xFFFFFFFF)
            $size += 8;
        return $size;
    }

    public function get_type(): string {
        return $this->type;
    }

    public function parse(stream $payload, box_parser $parser) {
        while ($box = $parser->parse($payload))
            $this->children[] = $box;
    }

    public function copy_to(stream $dest) {
        $this->copy_box_header_to($dest);
        foreach ($this->children as $box)
            $box->copy_to($dest);
    }
}
