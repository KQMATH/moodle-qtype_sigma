define(['jquery', 'qtype_sigma/tex2max', 'qtype_sigma/visual-math-input'], function ($, Tex2Max, VisualMath) {

    let inputs = [];
    let removedPlaceholders = [];

    function processQuestionText(elementID) {
    }

    function getExistingValue(elementID) {
    }


    function listenForSubmit(formElementID) {

        $('#' + formElementID).submit(function () {
            /* Validations go here */
            let html = Y.one('#id_questiontexteditable').getHTML();
            // removedPlaceholders = ['[[input:ans1]][[validation:ans1]]'];
            let newHTML = addPlaceholders(html, removedPlaceholders);

            $('#id_questiontextinput').val(newHTML);
            Y.one('#id_questiontexteditable').setHTML(newHTML);


            alert($('#id_questiontextinput').val());
            return true;
        });
    }


    function findPlaceholders(text, type) {
        let inputRegExp = new RegExp('\\[\\[' + type + ':([a-zA-Z][a-zA-Z0-9_]*)]]', 'g');
        //console.log(inputRegExp.exec('[[input:ans1]][[input:ans2]]'));
        //console.log('[[input:ans12]][[input:ans2]]'.match(inputRegExp));
        let result = [];

        let counter = 0;
        let z;
        while ((z = inputRegExp.exec(text)) !== null) {
            console.log(z[1]);  // output: "a"
            result.push(z);
            counter++;
        }
        return result;
    }

    function getPlaceholderNames(array) {
        if (null === array && array.length > 0) return false;

        let names = [];

        array.forEach(e => {
            names.push(e[1]);
        });

        return names;
    }

    function getPlaceholderMatches(array) {
        if (null === array && array.length > 0) return false;

        let matches = [];

        array.forEach(e => {
            matches.push(e[0]);
        });

        return matches;
    }

    function addPlaceholders(text, placeholders) {
        let newText;
        let placeholdersAsString = placeholders.join('');


        newText = text + placeholdersAsString;

        return newText;
    }

    function removePlaceholders(text, placeholders) {
        let cleanedText = text;
        placeholders.forEach(e => {
            cleanedText = cleanedText.replace(e, "");
        });

        removedPlaceholders.push(...placeholders);
        return cleanedText;
    }

    /*function removePlaceholders(node, type) {
        let children = node.childNodes;

        if (children.nodeName == 'SCRIPT') return false; // don't mess with script tags

        for (let child in children) {
            if (!children.hasOwnProperty(child)) continue;

            if (children[child].childElementCount > 0) {
                console.log('Sub nodes found!')
                console.log(children[child])
                let success = false //replaceTextNode(regExp, newValue, child);
                if (success) return true;

            } else {
                if (null !== children[child].firstChild && children[child].firstChild.nodeType === Node.TEXT_NODE) {
                    let placeholders = [];
                    placeholders = findPlaceholders(children[child].firstChild.nodeValue, type);
                    let placeholders1 = getPlaceholderMatches(placeholders);

                    if (placeholders1.length > 0) {
                        //console.log(nodeList[child].firstChild);

                        placeholders1.forEach(e => {
                            let html = children[child].innerHTML;
                            html = html.replace(e, "");
                            children[child].innerHTML = html;
                        });
                        console.log('html ::' + type)
                        console.log(children[child].innerHTML)


                    }
                }
            }

        }

    }*/


    class PotentialResponseTree {
        constructor(prtName) {
            this.prtNodeFields = [];

            this.prtNodes = [];
            this.prtName = prtName;


            this.defaultFieldNames = {
                answertest: 'AlgEquiv',
                sans: '',
                tans: '',
                testoptions: '',
                quiet: '0',
                truescoremode: '=',
                truescore: 1, // If one node = 1 else 0
                truepenalty: '',
                truenextnode: '-1',   // stack_string('nodex', $node->name) line 728
                trueanswernote: '', // stack_string('answernotedefault' . $branch, array('prtname' => $prtname, 'nodename' => $name)) line 808
                falsescoremode: '=',
                falsescore: 0,
                falsepenalty: '',
                falsenextnode: '-1',   // stack_string('nodex', $node->name) line 728
                falseanswernote: '', // stack_string('answernotedefault' . $branch, array('prtname' => $prtname, 'nodename' => $name)) line 808
                truefeedback: '',
                falsefeedback: ''
            };

        }

        getPRTNodeFields() {
            return this.prtNodeFields;
        }

        getPRTNodes() {
            return this.prtNodes;
        }

        addPRTNode(node) {
            this.prtNodes.push(node);
        }

        addPRTNodes(nodes) {
            this.prtNodes.push(...nodes);
        }

        getNumberOfNodes() {
            return this.prtNodes.length;
        }

        createNewPRTNode(existingNode) {
            let node = {};
            if (existingNode) node = existingNode;

            let result = {};
            for (let key in node) {
                if (!node.hasOwnProperty(key)) continue;
                if (key in this.defaultFieldNames) result[key] = node[key];
            }

            this.addPRTNode(result);
        }

        createNewPRTNodes(existingNodes) {
            for (let key in existingNodes) {
                let existingNode = existingNodes[key];
                let node = {};
                if (existingNode) node = existingNode;

                let result = {};
                for (let key in node) {
                    if (!node.hasOwnProperty(key)) continue;
                    if (key in this.defaultFieldNames) result[key] = node[key];
                }

                this.addPRTNode(result);
            }
        }

        buildAllNodesFields() {
            let nodes = this.getPRTNodes();
            let hiddenFields = [];

            let nodeNr = 0;
            nodes.forEach(node => {
                node = this.prepareNode(node, nodeNr);

                let editorFields = this.checkForEditorFields(node);

                for (let key in node) {
                    if (!node.hasOwnProperty(key)) continue;

                    hiddenFields.push(this.createHiddenField(key, node[key], nodeNr));
                }

                editorFields.forEach(editorField => {
                    hiddenFields.push(...this.createHiddenEditorFields(editorField, nodeNr));
                });
                nodeNr++;
            });

            return hiddenFields;
        }

        prepareNode(node, nodeNr) {
            let preparedNode = node;


            if (nodeNr === 0) { // Only model answer
                if (nodeNr < this.getNumberOfNodes()) {
                    preparedNode['falsenextnode'] = '-1';
                }

            } else if (this.getNumberOfNodes() > 1) { // Multiple answers
                if (nodeNr < this.getNumberOfNodes()) {
                    preparedNode['falsenextnode'] = '' + nodeNr + 1;

                } else if (nodeNr === this.getNumberOfNodes()) {
                    preparedNode['falsenextnode'] = '-1';

                } else {
                    throw new Error('Inconsistent number of nodes in object');
                }
            } else {
                throw new Error('Inconsistent number of nodes in object');
            }

            preparedNode['truenextnode'] = '-1';


            return preparedNode;
        }


        checkForEditorFields(node) {
            //TODO create obj with the keys text, format, itemid....
            let editorFields = [];

            for (let key in node) {
                if (!node.hasOwnProperty(key)) continue;

                if (key.includes('format')) {

                    let editorField = {
                        'text': null,
                        'format': null,
                        'itemid': null
                    };

                    editorField.format = {[key]: node[key]};
                    delete node[key];
                    let secondKey = key.replace('format', '');

                    editorField.itemid = {[secondKey]: 0};

                    for (let key in node) {

                        if (!node.hasOwnProperty(key)) continue;
                        if (key === secondKey) {
                            editorField.text = {[key]: node[key]};
                            delete node[key];
                        }
                    }
                    editorFields.push(editorField);

                }
            }
            return editorFields;
        }

        createHiddenField(fieldName, value, nodeNr) {
            if (value === null) value = '';

            let id = 'id_' + this.prtName + fieldName + '_' + nodeNr;
            let name = this.prtName + fieldName + '[' + nodeNr + ']';

            let element = document.createElement("input");
            element.setAttribute('id', id);
            element.setAttribute('name', name);
            element.setAttribute('type', 'hidden');
            element.setAttribute('value', value);

            this.prtNodeFields.push(element);
            return element;
        }

        createHiddenEditorFields(editorField, nodeNr) {
            let textID = 'id_' + this.prtName + Object.keys(editorField.text)[0] + '_' + nodeNr;
            let textName = this.prtName + Object.keys(editorField.text)[0] + '[' + nodeNr + ']' + '[text]';
            let textValue = editorField.text[Object.keys(editorField.text)[0]];

            let formatID = 'menu' + this.prtName + Object.keys(editorField.format)[0] + '[' + nodeNr + ']' + 'format';
            let formatName = this.prtName + Object.keys(editorField.format)[0] + '[' + nodeNr + ']' + '[format]';
            let formatValue = editorField.format[Object.keys(editorField.format)[0]];

            let itemidName = this.prtName + Object.keys(editorField.itemid)[0] + '[' + nodeNr + ']' + '[itemid]';

            let elementText = document.createElement("input");
            elementText.setAttribute('id', textID);
            elementText.setAttribute('name', textName);
            elementText.setAttribute('type', 'hidden');
            elementText.setAttribute('value', textValue);

            let elementFormat = document.createElement("input");
            elementFormat.setAttribute('id', formatID);
            elementFormat.setAttribute('name', formatName);
            elementFormat.setAttribute('type', 'hidden');
            elementFormat.setAttribute('value', formatValue);

            let elementItemid = document.createElement("input");
            elementItemid.setAttribute('name', itemidName);
            elementItemid.setAttribute('type', 'hidden');
            elementItemid.setAttribute('value', '0');

            return [elementText, elementFormat, elementItemid];
        }
    }


    class AnswersGUI {

        constructor(prts) {
            // Fields
            this.hiddenElementsWrapper = null;
            this.answerSection = null;

            this.init(prts);
        }

        init(prts) {
            this.potentialResponseTrees = [];

            for (let existingPRT in prts) {
                if (!prts.hasOwnProperty(existingPRT)) continue;

                let prt = new PotentialResponseTree(prts[existingPRT].name);
                this.potentialResponseTrees.push(prt);

                prt.createNewPRTNodes(prts[existingPRT].nodes);
            }
        }

        /**
         * Build the main GUI.
         */
        start($mformID) {
            this.answerSection = $('#id_answerheader');

            let answerInputFields = this.buildAnswerInputFields();
            this.answerSection.append(answerInputFields);


            let addAnswerBtn = this.buildAddBtn();
            this.answerSection.append(addAnswerBtn);



            // Add all the hidden inputs (from existing nodes in DB) to the DOM.
            let hiddenFields = this.potentialResponseTrees[0].buildAllNodesFields();
            let $mform = $('#' + $mformID);
            this.hiddenElementsWrapper = document.createElement('div');
            this.hiddenElementsWrapper.setAttribute('id', 'hidden-elements-wrapper');
            this.hiddenElementsWrapper.setAttribute('style', 'display: none;');
            $mform.append(this.hiddenElementsWrapper);

            // this.potentialResponseTrees.forEach(prt => {
            for (let key in hiddenFields) {
                if (!hiddenFields.hasOwnProperty(key)) continue;
                this.hiddenElementsWrapper.append(hiddenFields[key]);
            }
        }

        updateHiddenFields() {
            let hiddenFields = this.potentialResponseTrees[0].buildAllNodesFields();

            for (let key in hiddenFields) {
                if (!hiddenFields.hasOwnProperty(key)) continue;
                this.hiddenElementsWrapper.append(hiddenFields[key]);
            }
        }

        updateAnswerInputFields() {
            let updatedAnswerFields = this.buildAnswerInputFields();
            this.answerSection.html('');
            this.answerSection.append(updatedAnswerFields);
        }

        buildAnswerInputFields() {
            let answerInputFields = [];

            this.potentialResponseTrees.forEach(prt => {
                prt.getPRTNodes().forEach(node => {
                    let sans = document.createElement('input');
                    sans.setAttribute('id', 'sans_0');
                    sans.addEventListener('input', evt => {
                        console.log("Changed!");

                    });

                    answerInputFields.push(sans);
                });
            });

            return answerInputFields;
        }

        buildAddBtn() {
            let newAnswerBtn = document.createElement('button');
            newAnswerBtn.setAttribute('id', ' newAnswerBtn');
            newAnswerBtn.setAttribute('type', 'button');

            newAnswerBtn.addEventListener('click', evt => {
                console.log("Clicked!");
                this.potentialResponseTrees[0].createNewPRTNode();
                this.updateHiddenFields();
                this.updateAnswerInputFields();
            });
            newAnswerBtn.innerHTML = "Add new answer";

            return newAnswerBtn;
        }
    }


    function applyWhenElementExists(selector, myFunction, intervalTime) {
        let interval = setInterval(function () {
            if ($(selector).length > 0) {
                myFunction();
                clearInterval(interval);
            }
        }, intervalTime);
    }

    return {
        initialize: ($mformID, $field, $editorFields) => {

            console.log($field);

            $(document).ready(function () {
                console.log('DOM is ready!')


                //setupExistingPRTs($field);

                let GUI = new AnswersGUI($field);
                GUI.start($mformID);


                /* ---------------- To be edited ---------------- */
                applyWhenElementExists('#id_questiontexteditable', function () {

                    listenForSubmit($mformID);

                    // Here, enter the code corresponding to the modification you wish to implement.
                    console.log('Atto Editor is loaded!')

                    let questionTextInput = $('#id_questiontextinput');
                    //removePlaceholders(node, 'input');
                    console.log("-------------------------")
                    //removePlaceholders(node, 'validation');

                    //let html = Y.one('#id_questiontexteditable').getHTML();
                    //console.log(getPlaceholderMatches(findPlaceholders(html, 'input')));
                    let html = questionTextInput.val();
                    console.log(html)
                    let validationPlaceholders = getPlaceholderMatches(findPlaceholders(html, 'validation'));
                    let inputPlaceholders = getPlaceholderMatches(findPlaceholders(html, 'input'));

                    let newHTML = html;
                    newHTML = removePlaceholders(newHTML, inputPlaceholders);
                    newHTML = removePlaceholders(newHTML, validationPlaceholders);

                    console.log(newHTML);
                    Y.one('#id_questiontexteditable').setHTML(newHTML);

                    console.log(removedPlaceholders)


                }, 50);

                /*
                console.log(document.readyState == 'complete');
                Y.on('pluginsloaded', () => {
                    console.log("ijijjjijjij");
                });

                console.log("ready!");

                let text = document.getElementById('id_questiontextinput').textContent;
                inputs = getPlaceholderNames(findPlaceholders(text, 'input'));
                console.log(inputs);
                /!* console.log('text')
                 console.log(text)
                 //extractPlaceholders(text);


                 console.log(children);
                 for (child in children) {
                     console.log(children[child]);
                 }
 *!/
                let questionTextInput = $('#id_questiontextinput');
                //removePlaceholders(node, 'input');
                console.log("-------------------------")
                //removePlaceholders(node, 'validation');

                //let html = Y.one('#id_questiontexteditable').getHTML();
                //console.log(getPlaceholderMatches(findPlaceholders(html, 'input')));
                let html = questionTextInput.val();

                let validationPlaceholders = getPlaceholderMatches(findPlaceholders(html, 'validation'));
                let inputPlaceholders = getPlaceholderMatches(findPlaceholders(html, 'input'));

                let newHTML = html;
                newHTML = removePlaceholders(newHTML, validationPlaceholders);
                newHTML = removePlaceholders(newHTML, inputPlaceholders);

                console.log(newHTML);
                Y.one('#id_questiontexteditable').setHTML(newHTML);

                console.log(removedPlaceholders)
                //test.parent().parent().parent().parent().parent().parent().hide();
*/
            });
        }
    };

});