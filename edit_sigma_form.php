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
 * Defines the editing form for the SIGMA question type.
 *
 * @package    qtype
 * @subpackage sigma
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/stack/edit_stack_form.php');

/**
 * SIGMA question editing form definition.
 *
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_sigma_edit_form extends qtype_stack_edit_form {
    /** @var options the STACK configuration settings. */
    protected $sigmaconfig = null;

    /** Patch up data from the database before a user edits it in the form. */
    public function set_data($question) {
//print_r($question);
        parent::set_data($question);
    }

    //TODO finish validation
    public function validation($fromform, $files) {
        $errors = parent::validation($fromform, $files);
        return $errors;
    }

    public function qtype() {
        return 'sigma';
    }

    public function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);

        if (!isset($question->options)) {
            return $question;
        }

        $opt = $question->options;
        $question->singlevars = $opt->singlevars;
        $question->addtimessign = $opt->addtimessign;
        $question->mathinputmode = $opt->mathinputmode;

        return $question;
    }

    protected function definition_inner($mform) {
        parent::definition_inner($mform);

        $this->sigmaconfig = qtype_sigma\utils::get_config();

        $mform->addElement('selectyesno', 'singlevars',
            get_string('singlevars', 'qtype_sigma'));
        $mform->setDefault('singlevars', $this->sigmaconfig->singlevars);
        $mform->addHelpButton('singlevars', 'singlevars', 'qtype_sigma');

        $mform->addElement('selectyesno', 'addtimessign',
            get_string('addtimessign', 'qtype_sigma'));
        $mform->setDefault('addtimessign', $this->sigmaconfig->addtimessign);
        $mform->addHelpButton('addtimessign', 'addtimessign', 'qtype_sigma');

        $mform->addElement('select', 'mathinputmode',
            get_string('mathinputmode', 'qtype_sigma'), qtype_sigma\options::get_math_input_mode_options());
        $mform->setDefault('mathinputmode', $this->sigmaconfig->mathinputmode);
        $mform->addHelpButton('mathinputmode', 'mathinputmode', 'qtype_sigma');
    }
}