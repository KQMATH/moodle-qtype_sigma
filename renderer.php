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


use qtype_sigma\output\debug_renderer;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/stack/renderer.php');
require_once(__DIR__ . '/classes/options.php');
require_once(__DIR__ . '/classes/output/debug_renderer.php');


/**
 * Generates the output for SIGMA questions.
 *
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_sigma_renderer extends qtype_stack_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $PAGE, $CFG;

        $debug = $CFG->debugdeveloper;

        $result = '';
        $result .= html_writer::div('', '', ['id' => 'controls_wrapper']);
        $result .= parent::formulation_and_controls($qa, $options);

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
                if (isset($response[$name . '_latex'])) {
                    $value = $response[$name . '_latex'];
                } else {
                    $value = "";

                }
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

        if ($CFG->debugdeveloper) {
            $result .= debug_renderer::render_debug_view($stackinputids, "", $latexinputids, $latexresponses);
        }

        $configParams = $this->getAMDConfigParams($question);
        $amdParams = array($debug, $prefix, $stackinputids, $latexinputids, $latexresponses, $configParams);
        $PAGE->requires->js_call_amd('qtype_sigma/input', 'initialize', $amdParams);


        return $result;
    }


    public function head_code(question_attempt $qa) {
        global $PAGE;

        parent::head_code($qa);

        $PAGE->requires->css('/question/type/sigma/visualmathinput/mathquill.css');
        $PAGE->requires->css('/question/type/sigma/visualmathinput/visual-math-input.css');
    }

    /**
     * @param $question
     * @return mixed
     * @throws \qtype_sigma\exception\sigma_exception
     */
    private function getAMDConfigParams($question) {
        $result = [];
        if (!isset($question->singlevars)) throw new \qtype_sigma\exception\sigma_exception('renderer: singlevars is not set');
        if (!isset($question->addtimessign)) throw new \qtype_sigma\exception\sigma_exception('renderer: addtimessign is not set');
        if (!isset($question->mathinputmode)) throw new \qtype_sigma\exception\sigma_exception('renderer: mathinputmode is not set');

        $result['singlevars'] = $question->singlevars;
        $result['addtimessign'] = $question->addtimessign;
        $result['mathinputmode'] = $question->mathinputmode;

        return $result;
    }
}