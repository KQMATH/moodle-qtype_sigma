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
 * SIGMA question type backup code.
 * @package    qtype
 * @subpackage sigma
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/stack/backup/moodle2/backup_qtype_stack_plugin.class.php');

/**
 * Provides the information to backup SIGMA questions
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_qtype_sigma_plugin extends backup_qtype_stack_plugin {

    /**
     * @return backup_plugin_element the qtype information to attach to question element.
     */
    protected function define_question_plugin_structure() {

        // Define the virtual plugin element with the condition to fulfill.
        $plugin = $this->get_plugin_element(null, '../../qtype', 'sigma');

        // Create one standard named plugin element (the visible container).
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect the visible container ASAP.
        $plugin->add_child($pluginwrapper);

        // Now create the necessary elements.
        $sigmaoptions = new backup_nested_element('sigmaoptions', ['id'], ['singlevars', 'mathinputmode']);
        $stackoptions = new backup_nested_element('stackoptions', array('id'),
            array('questionvariables', 'specificfeedback', 'specificfeedbackformat',
                'questionnote', 'questionsimplify', 'assumepositive', 'assumereal',
                'prtcorrect', 'prtcorrectformat', 'prtpartiallycorrect', 'prtpartiallycorrectformat',
                'prtincorrect', 'prtincorrectformat', 'multiplicationsign', 'sqrtsign',
                'complexno', 'inversetrig', 'matrixparens', 'variantsselectionseed'));

        $stackinputs = new backup_nested_element('stackinputs');
        $stackinput = new backup_nested_element('stackinput', array('id'),
            array('name', 'type', 'tans', 'boxsize', 'strictsyntax', 'insertstars',
                'syntaxhint', 'syntaxattribute', 'forbidwords', 'allowwords', 'forbidfloat', 'requirelowestterms',
                'checkanswertype', 'mustverify', 'showvalidation', 'options'));

        $stackprts = new backup_nested_element('stackprts');
        $stackprt = new backup_nested_element('stackprt', array('id'),
            array('name', 'value', 'autosimplify', 'feedbackvariables', 'firstnode'));

        $stackprtnodes = new backup_nested_element('stackprtnodes');
        $stackprtnode = new backup_nested_element('stackprtnode', array('id'),
            array('nodename', 'answertest', 'sans', 'tans', 'testoptions', 'quiet',
                'truescoremode', 'truescore', 'truepenalty', 'truenextnode',
                'trueanswernote', 'truefeedback', 'truefeedbackformat',
                'falsescoremode', 'falsescore', 'falsepenalty', 'falsenextnode',
                'falseanswernote', 'falsefeedback', 'falsefeedbackformat'));

        $stackqtests = new backup_nested_element('stackqtests');
        $stackqtest = new backup_nested_element('stackqtest', array('id'), array('testcase', 'timemodified'));

        $stackqtestinputs = new backup_nested_element('stackqtestinputs');
        $stackqtestinput = new backup_nested_element('stackqtestinput', array('id'),
            array('inputname', 'value'));

        $stackqtestexpecteds = new backup_nested_element('stackqtestexpecteds');
        $stackqtestexpected = new backup_nested_element('stackqtestexpected', array('id'),
            array('prtname', 'expectedscore', 'expectedpenalty', 'expectedanswernote'));

        // Note, we intentionally don't backup stack_qtest_results. That is derived data.

        $stackdeployedseeds = new backup_nested_element('stackdeployedseeds');
        $stackdeployedseed = new backup_nested_element('stackdeployedseed', array('id'), array('seed'));

        // Build the tree.
        $pluginwrapper->add_child($sigmaoptions);

        $pluginwrapper->add_child($stackoptions);

        $pluginwrapper->add_child($stackinputs);
        $stackinputs->add_child($stackinput);

        $pluginwrapper->add_child($stackprts);
        $stackprts->add_child($stackprt);
        $stackprt->add_child($stackprtnodes);
        $stackprtnodes->add_child($stackprtnode);

        $pluginwrapper->add_child($stackqtests);
        $stackqtests->add_child($stackqtest);
        $stackqtest->add_child($stackqtestinputs);
        $stackqtestinputs->add_child($stackqtestinput);
        $stackqtest->add_child($stackqtestexpecteds);
        $stackqtestexpecteds->add_child($stackqtestexpected);

        $pluginwrapper->add_child($stackdeployedseeds);
        $stackdeployedseeds->add_child($stackdeployedseed);

        // Set source to populate the data.
        $sigmaoptions->set_source_table('qtype_sigma_options', array('questionid' => backup::VAR_PARENTID));
        $stackoptions->set_source_table('qtype_stack_options', array('questionid' => backup::VAR_PARENTID));
        $stackinput->set_source_table('qtype_stack_inputs', array('questionid' => backup::VAR_PARENTID));
        $stackprt->set_source_table('qtype_stack_prts', array('questionid' => backup::VAR_PARENTID));
        $stackprtnode->set_source_table('qtype_stack_prt_nodes',
            array('questionid' => '../../../../../../../id', 'prtname' => '../../name'));
        $stackqtest->set_source_sql(
            'SELECT * FROM {qtype_stack_qtests} WHERE questionid = ? ORDER BY testcase',
            array(backup::VAR_PARENTID));
        $stackqtestinput->set_source_table('qtype_stack_qtest_inputs',
            array('questionid' => '../../../../../../../id', 'testcase' => '../../testcase'));
        $stackqtestexpected->set_source_table('qtype_stack_qtest_expected',
            array('questionid' => '../../../../../../../id', 'testcase' => '../../testcase'));
        $stackdeployedseed->set_source_sql(
            'SELECT * FROM {qtype_stack_deployed_seeds} WHERE questionid = ? ORDER BY id',
            array(backup::VAR_PARENTID));

        return $plugin;
    }
}
