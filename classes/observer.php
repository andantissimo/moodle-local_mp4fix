<?php
/**
 * @package    local_mp4fix
 * @copyright  2017 MALU {@link https://github.com/andantissimo}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_mp4fix;

final class observer {

    public static function mod_created(\core\event\course_module_created $event) {
        $component = 'mod_' . $event->other['modulename'];
        self::fix_mp4_in_area($event->contextid, $component, 'content');
    }

    public static function mod_updated(\core\event\course_module_updated $event) {
        $component = 'mod_' . $event->other['modulename'];
        self::fix_mp4_in_area($event->contextid, $component, 'content');
    }

    private static function fix_mp4_in_area(int $contextid, string $component, string $area) {
        if (PHP_INT_SIZE < 8)
            return;
        $files = get_file_storage()->get_area_files($contextid, $component, $area);
        $mp4files = array_filter($files, function (\stored_file $file) {
            return preg_match('#^(video|audio)/mp4$#', $file->get_mimetype());
        });
        if (empty($mp4files))
            return;
        $tempdir = make_temp_directory("local_mp4fix/$contextid/$component/$area");
        foreach ($mp4files as $file) try {
            $stream = io\fstream::attach($file->get_content_file_handle());
            $movie = movie::parse($stream);
            if ($movie->is_progressive)
                continue;
            $ftyp = $movie->get_ftyp_box();
            $moov = $movie->get_moov_box();
            $movie->adjust_offsets($moov->get_size());
            $tempmovie = new movie;
            $tempmovie->add($ftyp);
            $tempmovie->add($moov);
            foreach ($movie as $box) {
                if ($box === $ftyp || $box === $moov)
                    continue;
                $tempmovie->add($box);
            }
            $tempmovie->get_moov_box()->get_mvhd_box()->modification_time = new \DateTime;
            $temppath = $tempdir . '/' . $file->get_filename();
            $tempstream = io\fstream::create($temppath);
            $tempmovie->copy_to($tempstream);
            unset($tempmovie, $tempstream, $movie, $stream);
            $filerecord = self::create_filerecord_from_stored_file($file);
            $filerecord->timemodified = time();
            $file->delete();
            get_file_storage()->create_file_from_pathname($filerecord, $temppath);
            unlink($temppath);
        } catch (\moodle_exception $ex) {
            debugging((string)$ex);
            continue;
        }
    }

    private static function create_filerecord_from_stored_file(\stored_file $file) {
        $filerecord = new \stdClass;
        $filerecord->contextid    = $file->get_contextid();
        $filerecord->component    = $file->get_component();
        $filerecord->filearea     = $file->get_filearea();
        $filerecord->itemid       = $file->get_itemid();
        $filerecord->filepath     = $file->get_filepath();
        $filerecord->filename     = $file->get_filename();
        $filerecord->timecreated  = $file->get_timecreated();
        $filerecord->timemodified = $file->get_timemodified();
        $filerecord->mimetype     = $file->get_mimetype();
        $filerecord->userid       = $file->get_userid();
        $filerecord->source       = $file->get_source();
        $filerecord->author       = $file->get_author();
        $filerecord->license      = $file->get_license();
        $filerecord->status       = $file->get_status();
        $filerecord->sortorder    = $file->get_sortorder();
        return $filerecord;
    }
}
