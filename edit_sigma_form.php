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
require_once(__DIR__ . '/classes/options.php');

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
    protected $editmode = null;

    /** Patch up data from the database before a user edits it in the form. */
    public function set_data($question) {
        parent::set_data($question);
    }

    //TODO finish validation
    public function validation($fromform, $files) {
        if ($this->editmode === MODE_SIMPLE) {
            $errors = question_edit_form::validation($fromform, $files);

        } else if ($this->editmode === MODE_ADVANCED) {
            $errors = parent::validation($fromform, $files);
        }

        return $errors;
    }

    public function qtype() {
        return 'sigma';
    }

    public function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);

        // Set the new "questiontextinput" input field to replace the old standard "questiontext" input field.
        $question->questiontextinput = $question->questiontext['text'];

        if (!isset($question->options)) {
            return $question;
        }
        $opt = $question->options;
        $question->editmode = $opt->editmode;
        $question->singlevars = $opt->singlevars;
        $question->addtimessign = $opt->addtimessign;
        $question->mathinputmode = $opt->mathinputmode;

        return $question;
    }

    protected function definition() {
        $this->sigmaconfig = qtype_sigma\utils::get_config();
        $mform = $this->_form;

        question_edit_form::definition();

        // Override standard "general feedback"
        $gf = $mform->getElement('generalfeedback');
        $gf->setLabel(get_string('suggestedsolution', 'qtype_sigma'));
        $mform->addHelpButton('generalfeedback', 'suggestedsolution', 'qtype_sigma');

        /*
                // Override the standard "question text editor textarea"
                $oldquestiontext = &$mform->getElement('questiontext');
                $newquestiontext = $mform->createElement('editor', 'questiontextinput', get_string('questiontext', 'question'),
                    array('rows' => 15), $this->editoroptions);
                $oldquestiontext = $newquestiontext;
                //$oldquestiontext->setName('questiontextinput');
                //$mform->setType('questiontextinput', PARAM_RAW);
        */


        if ($this->editmode === MODE_ADVANCED) {
            $fixdollars = $mform->createElement('checkbox', 'fixdollars',
                stack_string('fixdollars'), stack_string('fixdollarslabel'));
            $mform->insertElementBefore($fixdollars, 'buttonar');
            $mform->addHelpButton('fixdollars', 'fixdollars', 'qtype_stack');
            $mform->closeHeaderBefore('fixdollars');
        }


        $editmodeelem = $mform->createElement('select', 'editmode', '', qtype_sigma\options::get_edit_mode_options());
        $convertmode = $mform->createElement('submit', 'editmodebtn', get_string('editmodebtn', 'qtype_sigma'));

        $groupelements = [$editmodeelem, $convertmode];
        $editmodegroup = $mform->createElement('group', 'editmodegroup', get_string('editmode', 'qtype_sigma'), $groupelements, null, false);
        $mform->insertElementBefore($editmodegroup, 'buttonar');

        $mform->addHelpButton('editmodegroup', 'editmode', 'qtype_sigma');
        $mform->setDefault('editmode', 0); // TODO chancg 1 to $this->sigmaconfig->editmode when we have made admin settings for qtype_sigma
        $mform->registerNoSubmitButton('editmodebtn');


        // There is no un-closeHeaderBefore, so fake it.
        $closebeforebuttonarr = array_search('buttonar', $mform->defaultRenderer()->_stopFieldsetElements);
        if ($closebeforebuttonarr !== false) {
            unset($mform->defaultRenderer()->_stopFieldsetElements[$closebeforebuttonarr]);
        }
    }

    public function definition_after_data() {
        $mform = $this->_form;

        $oldquestiontext = &$mform->getElement('questiontext');

        $test = &$mform->getElement('questiontext');
        //print_r($test->getValue());
        //$mform->hideif('questiontext', 'editmode', 'eq', '0');

    }

    protected function definition_inner($mform) {
        global $PAGE;

        if (isset($this->question->options)) {
            $this->editmode = $this->question->options->editmode;
        } else {
            //TODO $this->editmode = $this->sigmaconfig->editmode
            $this->editmode = '0';
        }


        if ($this->editmode === MODE_SIMPLE) {

            $this->definition_inner_simple($mform);

            $this->sigmaconfig = qtype_sigma\utils::get_config();


            $mform->addElement('hidden', 'singlevars', $this->sigmaconfig->singlevars);
            $mform->setType('singlevars', PARAM_RAW);

            $mform->addElement('hidden', 'addtimessign', $this->sigmaconfig->addtimessign);
            $mform->setType('addtimessign', PARAM_RAW);

            $mform->addElement('hidden', 'mathinputmode', $this->sigmaconfig->mathinputmode);
            $mform->setType('mathinputmode', PARAM_RAW);


            $amdParams = [];
            $amdParams['mformid'] = $mform->getAttribute('id');
            //$amdParams['questiontext'] = $this->question->questiontext['text'];
            $amdParams[] = $this->question->prts;
            $amdParams[] = array("name" => "John", "hobby" => "hiking");
            $PAGE->requires->js_call_amd('qtype_sigma/edit-form', 'initialize', $amdParams);

        } elseif ($this->editmode === MODE_ADVANCED) {
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

    protected function definition_inner_simple($mform) {
        $this->stackconfig = stack_utils::get_config();

        // Prepare input types.
        $this->typechoices = stack_input_factory::get_available_type_choices();

        // Prepare answer test types.
        $answertests = stack_ans_test_controller::get_available_ans_tests();
        // Algebraic Equivalence should be the default test, and first on the list.
        // This does not come first in the alphabet of all languages.
        $default = 'AlgEquiv';
        $defaultstr = stack_string($answertests[$default]);
        unset($answertests[$default]);

        $this->answertestchoices = array();
        foreach ($answertests as $test => $string) {
            $this->answertestchoices[$test] = stack_string($string);
        }
        stack_utils::sort_array($this->answertestchoices);
        $this->answertestchoices = array_merge(array($default => $defaultstr),
            $this->answertestchoices);

        // Prepare score mode choices.
        $this->scoremodechoices = array(
            '=' => '=',
            '+' => '+',
            '-' => '-',
        );

        $inputnames = $this->get_input_names_from_question_text();
        $prtnames = $this->get_prt_names_from_question(); //TYTY

        // Note that for the editor elements, we are using $mform->getElement('prtincorrect')->setValue(...); instead
        // of setDefault, because setDefault does not work for editors.

        $mform->addElement('hidden', 'questiontextinput', null, array('id' => 'id_questiontextinput'));
        $mform->setType('questiontextinput', PARAM_RAW);
        /*        $mform->addElement('hidden', 'questiontext[format]', FORMAT_HTML);
                $mform->setType('questiontext[format]', PARAM_RAW);*/

        $mform->addHelpButton('questiontext', 'questiontext', 'qtype_stack');
        $mform->addRule('questiontext', stack_string('questiontextnonempty'), 'required', '', 'client');

        $qvars = $mform->createElement('textarea', 'questionvariables',
            stack_string('questionvariables'), array('rows' => 5, 'cols' => 80));
        $mform->insertElementBefore($qvars, 'questiontext');
        $mform->addHelpButton('questionvariables', 'questionvariables', 'qtype_stack');

        if (array_key_exists('id', $this->question)) {
            $urlparams = array('questionid' => $this->question->id, 'seed' => 0);
            $qtestlink = html_writer::link(new moodle_url(
                '/question/type/stack/questiontestrun.php', $urlparams),
                stack_string('runquestiontests'), array('target' => '_blank'));
            $qtlink = $mform->createElement('static', 'qtestlink', '', $qtestlink);
            $mform->insertElementBefore($qtlink, 'questionvariables');
        }

        $seed = $mform->createElement('text', 'variantsselectionseed',
            stack_string('variantsselectionseed'), array('size' => 50));
        $mform->insertElementBefore($seed, 'questiontext');
        $mform->setType('variantsselectionseed', PARAM_RAW);
        $mform->addHelpButton('variantsselectionseed', 'variantsselectionseed', 'qtype_stack');


        $mform->addElement('hidden', 'specificfeedback[text]', self::DEFAULT_SPECIFIC_FEEDBACK, array('id' => 'id_specificfeedback'));
        $mform->setType('specificfeedback[text]', PARAM_RAW);
        $mform->addElement('hidden', 'specificfeedback[format]', FORMAT_HTML);
        $mform->setType('specificfeedback[format]', PARAM_RAW);
        $mform->addElement('hidden', 'specificfeedback[itemid]', '0'); // TODO fix itemid
        $mform->setType('specificfeedback[itemid]', PARAM_RAW);


        $mform->addElement('textarea', 'questionnote',
            stack_string('questionnote'), array('rows' => 2, 'cols' => 80));
        $mform->addHelpButton('questionnote', 'questionnote', 'qtype_stack');

        $mform->addElement('submit', 'verify', stack_string('verifyquestionandupdate'));
        $mform->registerNoSubmitButton('verify');

        // Inputs.
        foreach ($inputnames as $inputname => $counts) {
            $this->definition_input($inputname, $mform, $counts);
        }

        // Answer/PRT.
        $this->definition_answer($mform);

        // PRTs.
        foreach ($prtnames as $prtname => $count) {
            //print_r($prtnames);
            $this->definition_prt($prtname, $mform, $count);
        }

        // Options.
        $mform->addElement('header', 'optionsheader', stack_string('options'));


        $mform->addElement('hidden', 'questionsimplify', $this->stackconfig->questionsimplify);
        $mform->setType('questionsimplify', PARAM_RAW);

        $mform->addElement('hidden', 'assumepositive', $this->stackconfig->assumepositive);
        $mform->setType('assumepositive', PARAM_RAW);

        $mform->addElement('hidden', 'assumereal', $this->stackconfig->assumereal);
        $mform->setType('assumereal', PARAM_RAW);

        $mform->addElement('hidden', 'questionsimplify', $this->stackconfig->questionsimplify);
        $mform->setType('questionsimplify', PARAM_RAW);

        $mform->addElement('hidden', 'questionsimplify', $this->stackconfig->questionsimplify);
        $mform->setType('questionsimplify', PARAM_RAW);


        $mform->addElement('editor', 'prtcorrect',
            stack_string('prtcorrectfeedback'),
            array('rows' => 1), $this->editoroptions);
        $mform->getElement('prtcorrect')->setValue(array(
            'text' => $this->stackconfig->prtcorrect));

        /*
                 $mform->addElement('editor', 'prtpartiallycorrect',
                    stack_string('prtpartiallycorrectfeedback'),
                    array('rows' => 1), $this->editoroptions);
                $mform->getElement('prtpartiallycorrect')->setValue(array(
                    'text' => $this->stackconfig->prtpartiallycorrect));
        */


        $mform->addElement('hidden', 'prtpartiallycorrect[text]', $this->stackconfig->prtpartiallycorrect);
        $mform->setType('prtpartiallycorrect[text]', PARAM_RAW);
        $mform->addElement('hidden', 'prtpartiallycorrect[format]', FORMAT_HTML);
        $mform->setType('prtpartiallycorrect[format]', PARAM_RAW);
        $mform->addElement('hidden', 'prtpartiallycorrect[itemid]', '0'); // TODO fix itemid
        $mform->setType('prtpartiallycorrect[itemid]', PARAM_RAW);

        $mform->addElement('editor', 'prtincorrect',
            stack_string('prtincorrectfeedback'),
            array('rows' => 1), $this->editoroptions);
        $mform->getElement('prtincorrect')->setValue(array(
            'text' => $this->stackconfig->prtincorrect));


        $mform->addElement('hidden', 'multiplicationsign', $this->stackconfig->multiplicationsign);
        $mform->setType('multiplicationsign', PARAM_RAW);

        $mform->addElement('hidden', 'sqrtsign', $this->stackconfig->sqrtsign);
        $mform->setType('sqrtsign', PARAM_RAW);

        $mform->addElement('hidden', 'complexno', $this->stackconfig->complexno);
        $mform->setType('complexno', PARAM_RAW);

        $mform->addElement('hidden', 'inversetrig', $this->stackconfig->inversetrig);
        $mform->setType('inversetrig', PARAM_RAW);

        $mform->addElement('hidden', 'matrixparens', $this->stackconfig->matrixparens);
        $mform->setType('matrixparens', PARAM_RAW);


        // Hints.
        $this->add_interactive_settings();


        // Replace standard penalty input at the bottom with the one we want.
        $mform->removeElement('multitriesheader');
        $mform->removeElement('penalty');

        $pen = $mform->createElement('text', 'penalty', stack_string('penalty'), array('size' => 5));
        $mform->insertElementBefore($pen, 'generalfeedback');
        $mform->setType('penalty', PARAM_FLOAT);
        $mform->addHelpButton('penalty', 'penalty', 'qtype_stack');
        $mform->setDefault('penalty', 0.1000000);
        $mform->addRule('penalty', null, 'required', null, 'client');
    }

    protected function definition_input($inputname, MoodleQuickForm $mform, $counts) {
        if ($this->editmode === MODE_ADVANCED) {
            parent::definition_input($inputname, $mform, $counts);

        } else if ($this->editmode === MODE_SIMPLE) {

            $mform->addElement('header', $inputname . 'header', stack_string('inputheading', $inputname));

            if ($counts[self::INPUTS] == 0 && $counts[self::VALIDATIONS] == 0) {
                $mform->addElement('static', $inputname . 'warning', '', stack_string('inputwillberemoved', $inputname));
                $mform->addElement('advcheckbox', $inputname . 'deleteconfirm', '', stack_string('inputremovedconfirm'));
                $mform->setDefault($inputname . 'deleteconfirm', 0);
                $mform->setExpanded($inputname . 'header');
            }

            $mform->addElement('select', $inputname . 'type', stack_string('inputtype'), $this->typechoices);
            $mform->setDefault($inputname . 'type', $this->stackconfig->inputtype);
            $mform->addHelpButton($inputname . 'type', 'inputtype', 'qtype_stack');

            $mform->addElement('text', $inputname . 'modelans', stack_string('teachersanswer'), array('size' => 20));
            $mform->setType($inputname . 'modelans', PARAM_RAW);
            $mform->addHelpButton($inputname . 'modelans', 'teachersanswer', 'qtype_stack');
            // We don't make modelans a required field in the formslib sense, because
            // That stops the input sections collapsing by default. Instead, we enforce
            // that it is non-blank in the server-side validation.

            $mform->addElement('hidden', $inputname . 'boxsize', $this->stackconfig->inputboxsize);
            $mform->setType($inputname . 'boxsize', PARAM_INT);

            $mform->addElement('hidden', $inputname . 'strictsyntax', $this->stackconfig->inputstrictsyntax);
            $mform->setType($inputname . 'strictsyntax', PARAM_RAW);

            $mform->addElement('hidden', $inputname . 'insertstars', $this->stackconfig->inputinsertstars);
            $mform->setType($inputname . 'insertstars', PARAM_RAW);

            $mform->addElement('hidden', $inputname . 'syntaxhint');
            $mform->setType($inputname . 'syntaxhint', PARAM_RAW);

            $mform->addElement('hidden', $inputname . 'syntaxattribute', '0');
            $mform->setType($inputname . 'syntaxattribute', PARAM_RAW);

            $mform->addElement('hidden', $inputname . 'forbidwords', $this->stackconfig->inputforbidwords);
            $mform->setType($inputname . 'forbidwords', PARAM_RAW);

            $mform->addElement('hidden', $inputname . 'allowwords', '');
            $mform->setType($inputname . 'allowwords', PARAM_RAW);

            $mform->addElement('hidden', $inputname . 'forbidfloat', $this->stackconfig->inputforbidfloat);
            $mform->setType($inputname . 'forbidfloat', PARAM_RAW);

            $mform->addElement('hidden', $inputname . 'requirelowestterms', $this->stackconfig->inputrequirelowestterms);
            $mform->setType($inputname . 'requirelowestterms', PARAM_RAW);

            $mform->addElement('hidden', $inputname . 'checkanswertype', $this->stackconfig->inputcheckanswertype);
            $mform->setType($inputname . 'checkanswertype', PARAM_RAW);


            $mform->addElement('selectyesno', $inputname . 'mustverify',
                stack_string('mustverify'));
            $mform->setDefault($inputname . 'mustverify', $this->stackconfig->inputmustverify);
            $mform->addHelpButton($inputname . 'mustverify', 'mustverify', 'qtype_stack');

            $mform->addElement('select', $inputname . 'showvalidation',
                stack_string('showvalidation'), stack_options::get_showvalidation_options());
            $mform->setDefault($inputname . 'showvalidation', $this->stackconfig->inputshowvalidation);
            $mform->addHelpButton($inputname . 'showvalidation', 'showvalidation', 'qtype_stack');


            $mform->addElement('hidden', $inputname . 'options');
            $mform->setType($inputname . 'options', PARAM_RAW);
        }
    }

    /**
     * Add the form elements defining one PRT.
     * @param string $prtname the name of the PRT.
     * @param MoodleQuickForm $mform the form being assembled.
     * @param int $count the number of times this PRT appears in the text of the question.
     */
    protected function definition_answer(MoodleQuickForm $mform) {
        //print_r(" FUNC 13 :: ");

        $mform->addElement('header', 'answerheader', 'Answer');

        $prtnames = $this->get_prt_names_from_question(); //TYTY
        foreach ($prtnames as $prtname => $count) break;

        $mform->addElement('text', $prtname . 'value', stack_string('questionvalue'), array('size' => 3));
        $mform->setType($prtname . 'value', PARAM_FLOAT);
        $mform->setDefault($prtname . 'value', 1);

        $mform->addElement('selectyesno', $prtname . 'autosimplify',
            stack_string('autosimplify'));
        $mform->setDefault($prtname . 'autosimplify', true);
        $mform->addHelpButton($prtname . 'autosimplify', 'autosimplifyprt', 'qtype_stack');


        $mform->addElement('static', null, 'Answers', 'answerplaceholder');

        //TODO CREATE PRT WITH JAVASCRIPT
    }
}