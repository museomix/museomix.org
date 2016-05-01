<?php
/*********************************************************************************
 * WP Ultimate CSV Importer is a Tool for importing CSV for the Wordpress
 * plugin developed by Smackcoder. Copyright (C) 2014 Smackcoders.
 *
 * WP Ultimate CSV Importer is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License version 3
 * as published by the Free Software Foundation with the addition of the
 * following permission added to Section 15 as permitted in Section 7(a): FOR
 * ANY PART OF THE COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY WP Ultimate
 * CSV Importer, WP Ultimate CSV Importer DISCLAIMS THE WARRANTY OF NON
 * INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * WP Ultimate CSV Importer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program; if not, see http://www.gnu.org/licenses or write
 * to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301 USA.
 *
 * You can contact Smackcoders at email address info@smackcoders.com.
 *
 * The interactive user interfaces in original and modified versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * WP Ultimate CSV Importer copyright notice. If the display of the logo is
 * not reasonably feasible for technical reasons, the Appropriate Legal
 * Notices must display the words
 * "Copyright Smackcoders. 2015. All rights reserved".
 ********************************************************************************/

require_once "SmackCSVObserver.php";

class SmackCSVLogger
{

    /**
     * SmackCSVLogger constructor.
     * @param null $log_file
     * @throws Exception
     */
    function __construct($log_file = null)
    {
        if (!$log_file)
        {
            $log_file = WP_CONST_ULTIMATE_CSV_IMP_DIRECTORY . $this->log_file;
        }

        if (!file_exists($log_file))
        {
            touch($log_file);
        }

        if (!(is_writable($log_file) || $this->win_is_writable($log_file)))
        {
            //Cant write to file,
            throw new Exception("LOGGER ERROR: Can't write to log", 1);
        }

    }

    /**
     * logD - Log Debug
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @return void
     **/
    public function logD($tag, $message)
    {
        $this->writeToLog("DEBUG", $tag, $message);
    }

    /**
     * logE - Log Error
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @author
     **/
    public function logE($tag, $message)
    {
        $this->writeToLog("ERROR", $tag, $message);
    }

    /**
     * logW - Log Warning
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @author
     **/
    public function logW($tag, $message)
    {
        $this->writeToLog("WARNING", $tag, $message);
    }

    /**
     * logI - Log Info
     * @param String tag - Log Tag
     * @param String message - message to spit out
     * @return void
     **/
    public function logI($tag, $message)
    {
        $this->writeToLog("INFO", $tag, $message);
    }

    /**
     * writeToLog - writes out timestamped message to the log file as
     * defined by the $log_file class variable.
     *
     * @param String status - "INFO"/"DEBUG"/"ERROR"/"WARNING"/"ALL"/"NONE"
     * @param String tag - "Small tag to help find log entries"
     * @param String message - The message you want to output.
     * @return void
     **/
    private function writeToLog($status, $tag, $message)
    {
        if ($this->log_status == "ALL" || $this->log_status == $status)
        {
            $date = date('[Y-m-d H:i:s]');
            $msg = "$date: [$tag][$status] - $message" . PHP_EOL;
            file_put_contents($this->log_file, $msg, FILE_APPEND);
        }
    }

    /**
     * win_is_writable function lifted from WordPress
     * @param $path
     * @return bool
     */
    private function win_is_writable($path)
    {
        if ($path[ strlen($path) - 1 ] == '/')
            return win_is_writable($path . uniqid(mt_rand()) . '.tmp');
        else if (is_dir($path))
            return win_is_writable($path . '/' . uniqid(mt_rand()) . '.tmp');

        $should_delete_tmp_file = !file_exists($path);
        $f = @fopen($path, 'a');
        if ($f === false)
            return false;

        fclose($f);

        if ($should_delete_tmp_file)
            unlink($path);

        return true;
    }
}
