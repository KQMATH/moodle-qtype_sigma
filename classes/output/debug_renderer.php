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


namespace qtype_sigma\output;

defined('MOODLE_INTERNAL') || die();


class debug_renderer {
    /**
     * Static class. You cannot create instances.
     * @throws \qtype_sigma\exception\sigma_exception
     */
    private function __construct() {
        throw new \qtype_sigma\exception\sigma_exception('debug_view: you cannot create instances of this class.');
    }

    public static function render_debug_header() {
        $result = "";

        $result .= \html_writer::start_tag('h5');
        $result .= \html_writer::span('!', 'sigma-debug-nb-star');
        $result .= \html_writer::span('Debugging info:');
        $result .= \html_writer::end_tag('h5');
        return $result;
    }

    public static function render_debug_stack_span($inputid, $value) {
        $result = "";

        $attributes = array(
            'id' => $inputid . '_debug'
        );

        $result .= \html_writer::span("Transpiled Maximxa code from TeX2Max: ");
        $result .= \html_writer::div($value, 'sigma-debug-value', $attributes);

        return $result;
    }

    public static function render_debug_latex_span($inputid, $value) {
        $result = "";

        $attributes = array(
            'id' => $inputid . '_debug'
        );

        $result .= \html_writer::span("LaTeX from MathQuill: ");
        $result .= \html_writer::div($value, 'sigma-debug-value', $attributes);


        return $result;
    }


    public static function render_debug_view(array $stackinputs,  $stackinputstring, array $latexinputs, array $latexinputstring) {

        $result = "";
        $result .= \html_writer::start_div('sigma-debug-wrapper');

        $result .= self::render_debug_header();


        for ($i = 0; $i < count($stackinputs); $i++) {
            $result .= \html_writer::start_div('sigma-debug-question-wrapper');
            $result .= self::render_debug_stack_span($stackinputs[$i], $stackinputstring);
            $result .= self::render_debug_latex_span($latexinputs[$i], $latexinputstring[$i]);
            $result .= \html_writer::end_div();
        }

        $result .= \html_writer::end_div();

        return $result;
    }
}