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

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Options for new inputs.
    $settings->add(new admin_setting_heading('inputoptionsheading',
        get_string('input_options_heading', 'qtype_sigma'),
        get_string('input_options_heading_desc', 'qtype_sigma')));


    $settings->add(new admin_setting_configselect('qtype_sigma/singlevars',
        get_string('only_single_letter_variables', 'qtype_sigma'),
        get_string('only_single_letter_variables_desc', 'qtype_sigma'), '1',
        array(
            '0' => get_string('no'),
            '1' => get_string('yes'),
        )));

}