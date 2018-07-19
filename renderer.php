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
 * SIGMA question renderer class.
 *
 * @package    qtype
 * @subpackage sigma
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/stack/renderer.php');

/**
 * Generates the output for SIGMA questions.
 *
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_sigma_renderer extends qtype_stack_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $PAGE;

        $result = parent::formulation_and_controls($qa, $options);

        $response = $qa->get_last_qt_data();
        $prefix = $qa->get_field_prefix();
        $question = $qa->get_question();

        $stackinputids = [];
        $latexinputids = [];
        $latexresponses = [];

        foreach ($question->inputs as $name => $input) {
            // Collect all the STACK input field ids.
            if ($input->requires_validation()) {
                $stackinputids[] = $prefix . $name;
            }

            // Create new hidden input fields for string the raw LaTeX input.
            $latexinputname = $prefix . $name . '_latex';
            $latexinputids[] = $latexinputname;

            // Set initial question value to "" if the question_attempt has no responses.
            if ($qa->get_state() == question_state::$todo) {
                $value = "";
            } else {
                $value = $response[$name . '_latex'];
            }
            $latexresponses[] = $value;

            $attributes = array(
                'type' => 'hidden',
                'name' => $latexinputname,
                'value' => $value,
                'id' => $latexinputname,
            );
            $result .= html_writer::empty_tag('input', $attributes);
        }

        $amdParams = array($prefix, $stackinputids, $latexinputids, $latexresponses);
        $PAGE->requires->js_call_amd('qtype_sigma/input', 'initialize', $amdParams);

        return $result;
    }
}