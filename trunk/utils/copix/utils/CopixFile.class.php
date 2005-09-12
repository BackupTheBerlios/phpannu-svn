<?php
/**
* @package   copix
* @subpackage copixtools
* @version   $Id: CopixFile.class.php,v 1.4.4.1 2005/08/01 22:17:54 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* CopixFileLocker heir. Is heavily based on what you can see on Smarty (http://smarty.php.net)
*
* @package   copix
* @subpackage generaltools
* @author Gérald Croës.
*/
class CopixFile {
    /**
    * Reads the content of a file.
    * @param string $filename the fileanme we're gonna read
    * @return string the content of the file. false if cannot read the file
    */
    function read ($filename){
        if ( file_exists ($filename) ) {
            return file_get_contents ($filename, false);
        } else {
            return false;
        }
    }

    /**
    * Write a file to the disk.
    * This function is heavily based on the way smarty process its own files.
    * Is using a temporary file and then rename the file. We guess the file system will be smarter than us, avoiding a writing / reading
    *  while renaming the file.
    */
    function write ($file, $data){
        $_dirname = dirname($file);

        //asking to create the directory structure if needed.
        $this->_createDir ($_dirname);

        if(!@is_writable($_dirname)) {
            // cache_dir not writable, see if it exists
            if(!@is_dir($_dirname)) {
                trigger_error (CopixI18N::get ('copix:copix.error.cache.directoryNotExists', array ($_dirname)));
                return false;
            }
            trigger_error (CopixI18N::get ('copix:copix.error.cache.notWritable', array ($file, $_dirname)));
            return false;
        }

        // write to tmp file, then rename it to avoid
        // file locking race condition
        $_tmp_file = tempnam($_dirname, 'wrt');

        if (!($fd = @fopen($_tmp_file, 'wb'))) {
            $_tmp_file = $_dirname . '/' . uniqid('wrt');
            if (!($fd = @fopen($_tmp_file, 'wb'))) {
                trigger_error(CopixI18N::get ('copix:copix.error.cache.errorWhileWritingFile', array ($file, $_tmp_file)));
                return false;
            }
        }

        fwrite($fd, $data);
        fclose($fd);

        // Delete the file if it allready exists (this is needed on Win,
        // because it cannot overwrite files with rename())
        if (CopixConfig::osIsWindows () && file_exists($file)) {
            @unlink($file);
        }
        @rename($_tmp_file, $file);
        @chmod($file,  0644);

        return true;
    }

    /**
    * create directory structure.
    * @param string $dir the structure we're gonna try to create
    */
    function _createDir ($dir){
        if (!file_exists($dir)) {
            $_open_basedir_ini = ini_get('open_basedir');

            if (DIRECTORY_SEPARATOR=='/') {
                /* unix-style paths */
                $_dir = $dir;
                $_dir_parts = preg_split('!/+!', $_dir, -1, PREG_SPLIT_NO_EMPTY);
                $_new_dir = ($_dir{0}=='/') ? '/' : getcwd().'/';
                if($_use_open_basedir = !empty($_open_basedir_ini)) {
                    $_open_basedirs = explode(':', $_open_basedir_ini);
                }

            } else {
                /* other-style paths */
                $_dir = str_replace('\\','/', $dir);
                $_dir_parts = preg_split('!/+!', $_dir, -1, PREG_SPLIT_NO_EMPTY);
                if (preg_match('!^((//)|([a-zA-Z]:/))!', $_dir, $_root_dir)) {
                    /* leading "//" for network volume, or "[letter]:/" for full path */
                    $_new_dir = $_root_dir[1];
                    /* remove drive-letter from _dir_parts */
                    if (isset($_root_dir[3])) array_shift($_dir_parts);

                } else {
                    $_new_dir = str_replace('\\', '/', getcwd()).'/';

                }

                if($_use_open_basedir = !empty($_open_basedir_ini)) {
                    $_open_basedirs = explode(';', str_replace('\\', '/', $_open_basedir_ini));
                }

            }

            /* all paths use "/" only from here */
            foreach ($_dir_parts as $_dir_part) {
                $_new_dir .= $_dir_part;

                if ($_use_open_basedir) {
                    // do not attempt to test or make directories outside of open_basedir
                    $_make_new_dir = false;
                    foreach ($_open_basedirs as $_open_basedir) {
                        if (substr($_new_dir, 0, strlen($_open_basedir)) == $_open_basedir) {
                            $_make_new_dir = true;
                            break;
                        }
                    }
                } else {
                    $_make_new_dir = true;
                }

                if ($_make_new_dir && !file_exists($_new_dir) && !@mkdir($_new_dir, 0771) && !is_dir($_new_dir)) {
                    trigger_error(CopixI18N::get ("copix:copix.error.cache.creatingDirectory", array ($_new_dir)));
                    return false;
                }
                $_new_dir .= '/';
            }
        }
    }
}
?>