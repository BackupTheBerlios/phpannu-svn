<?php
/**
* @package   copix
* @subpackage copixtools
* @version   $Id: CopixFileLocker.class.php,v 1.10 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
* pseudo-système de verrou de fichier.
*
* Principe de fonctionnement
*
* Lors du lock, création d'un fichier vide ".lock" du même nom du fichier locké.
* Test du lock = test de l'existance du fichier.
* Ecriture protégée = test du lock. Si lock, attente de X delais Y fois pour Y tentatives supplémentaires.
* Lecture protégée = même principe.
* Libération du lock = suppression du fichier ".lock"
*
* @package   copix
* @subpackage generaltools
* @author Gérald Croës.
*/
class CopixFileLocker {
    /**
    * @var string l'extension des fichiers de lock.
    */
    var $lockExt;

    /**
    * constructeur
    * @param string $lockExt      l'extension du fichier de lock.
    * @param CopixDebug   $ptrObjectDebug   objet de debuggage
    */
    function CopixFileLocker ($lockExt='.lock'){
        $this->lockExt = $lockExt;
    }

    /**
    * Création du fichier verrou.
    * @param string  $fileName   Le nom du fichier a locker.
    * @return boolean   indique si il y a eu creation ou pas du fichier de lock
    */
    function lockFile ($fileName){
        if ($this->isLocked ($fileName)){
            return false;
        }else{
            $fp = fopen ($fileName.$this->lockExt, 'w');
            fclose ($fp);
            return file_exists ($fileName.$this->lockExt);
        }
    }

    /**
    * Indique si le fichier verrou est présent sur le système.
    * @param string   $fileName   le nom du fichier a tester.
    */
    function isLocked ($fileName){
        return file_exists($fileName.$this->lockExt);
    }

    /**
    * Supprime le fichier indicateur du verrou.
    * @param  string $fileName    le nom du fichier a libérer.
    * @return boolean   indique si le fichier a été libéré
    */
    function unlockFile ($fileName){
        if ($this->isLocked($fileName)){
            return unlink($fileName.$this->lockExt);
        }else{
            return false;
        }
    }

    /**
    * Demande d'écriture dans un fichier avec système de verrou.
    * @param   string   $File         nom du fichier
    * @param string   $DataToWrite   données à ecrire dans le fichier
    * @param string   $Mode         mode d'ouverture du fichier
    * @param integer   $CycleDelais    delais d'attente entre chaque tentative d'ouverture du fichier
    * @param   integer   $CycleCount      nombre de tentative
    * @return boolean   indique si l'ecriture dans le fichier a reussie
    */
    function write ($file, $dataToWrite, $mode="aw", $cycleDelais=200, $cycleCount=15){
        return $this->writeUsingTmpFile ($file, $dataToWrite);

        $waitingCycle = 0;//Nombre de cycle écoulé = 0.
        $writedDats = false;//Les données n'ont pas encore été écrites.

        while ($writedDats == false && ($waitingCycle <= $cycleCount) ){
            //tant que nombre de délais d'attente pas dépassé.
            if (!$this->lockFile ($file)){
                //impossible de locker le fichier, pause et incrémente le cycle.
                $waitingCycle++;
                usleep ($cycleDelais);
            }else{
                //écriture des données dans le fichier.
                $filePtr = fopen ($file, $mode);
                if ($filePtr){
                    fwrite ($filePtr, $dataToWrite);
                    fclose ($filePtr);
                }
                $this->unlockFile ($file);
                $writedDats=true;
            }
        }

        //Sortie de la boucle. (impossible d'écrire, traitement de l'erreur)
        return $writedDats;
    }

    /**
    * Demande de lecture du fichier avec système de verrou (ne pas lire lors de l'écriture)
    * @param   string   $file         nom du fichier
    * @param integer   $cycleDelais    delais d'attente entre chaque tentative d'ouverture du fichier
    * @param   integer   $cycleCount      nombre de tentative
    * @return boolean / String   retourne le contenu du fichier ou false si echec.
    */
    function read ($file, $cycleDelais=200, $cycleCount=15){
        return $this->readDirect ($file);

        if (is_readable ($file)){
            $waitingCycle = 0;      //Nombre de cycle écoulé = 0.
            $readedDats = false;   //Les données n'ont pas encore été lues.
            while ($readedDats == false && ($waitingCycle <= $cycleCount) ){
                //tant que nombre de délais d'attente pas dépassé.
                if (!$this->lockFile ($file)){
                    //impossible de locker le fichier, pause et incrémente le cycle.
                    $waitingCycle++;
                    usleep ($cycleDelais);
                }else{
                    //lecture des données depuis le fichier.
                    $toReturn = implode('', file ($file));
                    $this->unlockFile ($file);
                    return $toReturn;
                }
            }
            //Sortie de la boucle. (impossible d'écrire, traitement de l'erreur)
            return null;
        }else{
            return null;//fichier non trouvé.
        }
    }

    /**
    * Reads the content of a file.
    * @param string $filename the fileanme we're gonna read
    * @return string the content of the file. false if cannot read the file
    */
    function readDirect ($filename){
        if ( file_exists($filename) && ($fd = @fopen($filename, 'rb')) ) {
            $contents = ($size = filesize($filename)) ? fread($fd, $size) : '';
            fclose($fd);
            return $contents;
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
    function writeUsingTmpFile ($file, $data){
        $_dirname = dirname($file);

        //asking to create the directory structure if needed.
        $this->createDir ($_dirname);

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
        // because it cannot overwrite files with rename()
        if (file_exists($file)) {
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
    function createDir ($dir){
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