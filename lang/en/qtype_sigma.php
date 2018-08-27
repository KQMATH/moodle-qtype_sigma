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

// General.
$string['pluginname'] = 'SIGMA';
$string['pluginname_help'] = 'SIGMA is an assessment system based on STACK with visual math input';
$string['pluginname_link'] = 'question/type/sigma';
$string['pluginnameadding'] = 'Adding a SIGMA question';
$string['pluginnameediting'] = 'Editing a SIGMA question';
$string['pluginnamesummary'] = 'SIGMA provides mathematical questions with visual input capabilities for Moodle quizzes.';


// Admin Settings.
$string['input_options_heading'] = 'Default input options';
$string['input_options_heading_desc'] = 'Used when creating a new question, or adding a new input to an existing question.';
$string['only_single_letter_variables'] = 'Only single lettered variables';
$string['only_single_letter_variables_desc'] = 'Restrict inputs to only allow single lettered variables.';
$string['add_times_sign'] = 'Add times sign';
$string['add_times_sign_desc'] = 'Controls whether or not to add times sign. Note: this goes on top of STACK\'s functionality to insert stars.';
$string['math_input_mode'] = 'Math input mode';
$string['math_input_mode_desc'] = 'Controls which set of mathematical input buttons are to be displayed.';

// Edit modes.
$string['editmodesimple'] = 'Simple';
$string['editmodeadvanced'] = 'Advanced';

// Math input modes.
$string['modesimple'] = 'Simple';
$string['modenormal'] = 'Normal';
$string['modeadvanced'] = 'Advanced';
$string['modecalculus'] = 'Calculus';

// Strings used on the editing form.
$string['singlevars'] = 'Single vars';
$string['singlevars_help'] = 'Whether or not restrict inputs to only allow single lettered variables.';
$string['addtimessign'] = 'Add times sign';
$string['addtimessign_help'] = 'Whether or not to add times sign. This goes on top of STACk\'s functionality to insert stars.';
$string['mathinputmode'] = 'Math input mode';
$string['mathinputmode_help'] = 'Controls which set of mathematical input buttons are to be displayed.';
