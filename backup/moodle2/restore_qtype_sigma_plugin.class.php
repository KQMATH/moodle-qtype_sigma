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
 * SIGMA question type restore code.
 *
 * @package    qtype
 * @subpackage sigma
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/stack/backup/moodle2/restore_qtype_stack_plugin.class.php');

/**
 * Provides the necessary information needed to restore SIGMA questions
 *
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_sigma_plugin extends restore_qtype_stack_plugin
{

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure()
    {
        $paths = parent::define_question_plugin_structure();
        // This qtype uses question_answers, add them.
        $this->add_question_question_answers($paths);
        // Add own qtype stuff.
        $elename = 'qtype_sigma_options';
        $elepath = $this->get_pathfor('/sigmaoptions'); // We used get_recommended_name() so this works.
        $paths[] = new restore_path_element($elename, $elepath);
        return $paths; // And we return the interesting paths.
    }

    /**
     * Process the teh SIGMA options
     * @param array/object $data the data from the backup file.
     */
    public function process_qtype_sigma_options($data)
    {
        global $DB;

        $data = (object)$data;

        if (!property_exists($data, 'singlevars')) {
            $data->singlevars = '1';
        }

        if (!property_exists($data, 'mathinputmode')) {
            $data->mathinputmode = 'normal';
        }

        // Detect if the question is created or mapped.
        $questioncreated = (bool)$this->get_mappingid('question_created', $this->get_old_parentid('question'));

        // If the question is being created by restore, save the sigma options.
        if ($questioncreated) {
            $oldid = $data->id;
            $data->questionid = $this->get_new_parentid('question');
            $newitemid = $DB->insert_record('qtype_sigma_options', $data);
            $this->set_mapping('qtype_sigma_options', $oldid, $newitemid);
        }
    }
}
