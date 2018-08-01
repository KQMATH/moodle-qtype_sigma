define(['jquery', 'qtype_sigma/tex2max', 'qtype_sigma/visual-math-input'], function ($, Tex2Max, VisualMath) {


    function convert(latex, options) {
        let converter = new Tex2Max.TeX2Max(options);

        let result = '';

        try {
            result = converter.toMaxima(latex);
        } catch (error) {
            //TODO display error messages by replacing STACK's maxima response div "stackinputfeedback"
            console.log(error.message);
        }
        console.log(result);
        return result;
    }

    function showOrHideCheckButton(inputIDs, prefix) {
        for (let i = 0; i < inputIDs.length; i++) {
            let $outerdiv = $(document.getElementById(inputIDs[i])).parents('div.que.sigma').first();
            if ($outerdiv && ($outerdiv.hasClass('dfexplicitvaildate') || $outerdiv.hasClass('dfcbmexplicitvaildate'))) {
                // With instant validation, we don't need the Check button, so hide it.
                let button = $outerdiv.find('.im-controls input.submit').first();
                if (button.attr('id') === prefix + '-submit') {
                    button.hide();
                }
            }
        }
    }

    const DEFAULTS = {
        onlySingleVariables: false,
        handleEquation: false,
        addTimesSign: true,
        onlyGreekName: false,
        onlyGreekSymbol: false
    };

    function formatOptionsObj(rawOptions) {
        let options = {};

        for (let key in rawOptions) {
            if (!rawOptions.hasOwnProperty(key)) continue;

            let value = rawOptions[key];
            switch (key) {
                case "singlevars":
                    if (value === '1') {
                        options.onlySingleVariables = true;
                    } else {
                        options.onlySingleVariables = false;
                    }
                    break;
                case "addtimessign":
                    if (value === '1') {
                        options.addTimesSign = true;
                    } else {
                        options.addTimesSign = false;
                    }
                    break;

                default :
                    break;
            }

        }

        options = Object.assign(DEFAULTS, options);

        return options;
    }

    function buildInputControls(mode) {
        if (!mode) throw new Error('No mathinputmode is set');


        let controls = new VisualMath.ControlList('#controls_wrapper');
        let controlNames = [];

        switch (mode) {
            case 'simple':
                console.log(mode);
                controlNames = ['sqrt', 'divide', 'pi'];
                controls.enable(controlNames);
                break;
            case 'normal':
                console.log(mode);
                controlNames = ['sqrt', 'divide', 'pi'];
                controls.enableAll();
                break;
            case 'advanced':
                console.log(mode);
                controls.enableAll();
                break;
            case 'calculus':
                console.log(mode);
                controlNames = ['sqrt', 'int', 'dint', 'divide', 'plusminus', 'theta', 'pi', 'infinity'];
                controls.enable(controlNames);
                break;
            case 'none':
                console.log(mode);
                break;
            default:
                console.log('default');
                break;
        }

    }

    return {
        initialize: (prefix, stackInputIDs, latexInputIDs, latexResponses, questionOptions) => {

            let options = formatOptionsObj(questionOptions);
            let readOnly = false;

            showOrHideCheckButton(stackInputIDs, prefix);


            for (let i = 0; i < stackInputIDs.length; i++) {

                let latexInput = document.getElementById(latexInputIDs[i]);
                let $latexInput = $(latexInput);

                let stackInput = document.getElementById(stackInputIDs[i]);
                let $stackInput = $(stackInput);

                let $parent = $stackInput.parent();

                let input = new VisualMath.Input('#' + $.escapeSelector(stackInputIDs[i]), $parent);
                input.$input.hide();

                if (!input.$input.prop('readonly')) {
                    input.onEdit = ($input, field) => {
                        $input.val(convert(field.latex(), options));
                        $latexInput.val(field.latex());
                        $input.get(0).dispatchEvent(new Event('change')); // Event firing needs to be on a vanilla dom object.
                    };

                } else {
                    readOnly = true;
                    input.disable();
                }

                // Set the previous step attempt data or autosaved (mod_quiz) value to the MathQuill field.
                if ($latexInput.val()) {
                    input.field.write($latexInput.val());
                } else if (latexResponses[i] !== null && latexResponses[i] !== "") {
                    input.field.write(latexResponses[i]);
                }

            }


            if (!readOnly) buildInputControls(questionOptions['mathinputmode']);

        }
    };

});