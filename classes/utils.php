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
 * Utility class for SIGMA.
 *
 * @package    qtype
 * @subpackage sigma
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace qtype_sigma;

defined('MOODLE_INTERNAL') || die();

/**
 * Class providing different utility methods.
 *
 * @author     André Storhaug <andr3.storhaug+code@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {
    /** @var object the SIGMA config data, so we only ever have to load it from the DB once. */
    protected static $config = null;

    /**
     * Static class. You cannot create instances.
     * @throws exception\sigma_exception
     */
    private function __construct() {
        throw new exception\sigma_exception('utils: you cannot create instances of this class.');
    }

    /** Get the sigma configuration settings. */
    public static function get_config() {
        if (is_null(self::$config)) {
            self::$config = get_config('qtype_sigma');
        }
        return self::$config;
    }

    public static function clear_config_cache() {
        self::$config = null;
    }
}