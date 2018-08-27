<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * SIGMA options class.
 *
 * @package    qtype
 * @subpackage sigma
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace qtype_sigma;

/**
 * MODE_SIMPLE - advanced editing mode.
 */
define('MODE_SIMPLE', "0");

/**
 * MODE_ADVANCED - advanced editing mode.
 */
define('MODE_ADVANCED', "1");


/**
 * Represents a SIGMA option class.
 *
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class options {

    public static function is_question_single_vars() {
        $isSingleVars = false;

        $config = get_config('qtype_sigma');
        if ($config->singlevars === 1) {
            $isSingleVars = true;
        }
        return $isSingleVars;
    }

    /**
     * @return array of choices for a no/yes select menu.
     */
    public static function get_yes_no_options() {
        return array(
            '0' => get_string('no'),
            '1' => get_string('yes'),
        );
    }

    /**
     * @return array of choices for the form editing modes select menu.
     */
    public static function get_edit_mode_options() {
        return array(
            '0' => get_string('editmodesimple', 'qtype_sigma'),
            '1' => get_string('editmodeadvanced', 'qtype_sigma')
        );
    }

    /**
     * @return array of choices for the mathematical input modes select menu.
     */
    public static function get_math_input_mode_options() {
        return array(
            'simple' => get_string('modesimple', 'qtype_sigma'),
            'normal' => get_string('modenormal', 'qtype_sigma'),
            'advanced' => get_string('modeadvanced', 'qtype_sigma'),
            'calculus' => get_string('modecalculus', 'qtype_sigma'),
            'none' => get_string('none'),
        );
    }

}