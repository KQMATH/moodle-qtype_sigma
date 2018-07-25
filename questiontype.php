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
 * @package    qtype
 * @subpackage sigma
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/stack/questiontype.php');

/**
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_sigma extends qtype_stack {

    public function save_question_options($fromform) {
        global $DB;

        parent::save_question_options($fromform);

        $options = $DB->get_record('qtype_sigma_options', array('questionid' => $fromform->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $fromform->id;
            $options->singlevars = '';
            $options->addtimessign = '';
            $options->mathinputmode = '';
            $options->id = $DB->insert_record('qtype_sigma_options', $options);
        }
        $options->singlevars = $fromform->singlevars;
        $options->addtimessign = $fromform->addtimessign;
        $options->mathinputmode = $fromform->mathinputmode;
        $DB->update_record('qtype_sigma_options', $options);
    }


    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('qtype_sigma_options', array('questionid' => $questionid));
        parent::delete_question($questionid, $contextid);
    }


    public function get_question_options($question) {
        global $DB;

        parent::get_question_options($question);

        $stackoptions = $question->options;
        $sigmaoptions = $DB->get_record('qtype_sigma_options',
            array('questionid' => $question->id), '*', MUST_EXIST);

        $question->options = (object)array_merge((array)$stackoptions, (array)$sigmaoptions);

        return true;
    }


    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);

        $question->singlevars = $questiondata->options->singlevars;
        $question->addtimessign = $questiondata->options->addtimessign;
        $question->mathinputmode = $questiondata->options->mathinputmode;

    }

}