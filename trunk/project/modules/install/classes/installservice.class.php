<?php
/**
* @package  copix
* @subpackage install
* @version  $Id: installservice.class.php,v 1.3 2005/05/04 10:11:50 laurentj Exp $
* @author   Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/*
*
* Objet de distribution du framework
*
* Cherche les fichiers install.typedb.sql
* Crée le fichier de conf XML de CopixDB
* Execute les scripts dans la base courante
*/

class InstallService {
    /**
    * Check that the configuration file for the database is writabe
    */
    function checkConfigurationFileWritable (){
        return is_writable (XML_COPIXDB_PROFIL);
    }

    /**
    * setConn
    *
    * Create the XML copixDB connection profile
    *
    * @param string $userDB name for the database connection
    * @param string $passDB password for the database connection
    * @param string $hostDB host for the database connection
    * @param string $dbDB   database for the database connection
    * @param string $typeDB copix dbtype driver for the connection
    */
    function setConn($userDB, $passDB, $hostDB, $dbDB, $typeDB) {
        $filename = XML_COPIXDB_PROFIL;
        $XMLconn = '<?xml version="1.0" encoding="iso-8859-1"?>'."\r\n".
        '   <dbdefinition>'."\r\n".
        '       <general>'."\r\n".
        '           <defaultprofil name="Select" />'."\r\n".
        '       </general>'."\r\n".
        '       <profils>'."\r\n".
        '       <profil name="Select"'."\r\n".
        '           driver="' . $typeDB . '"'."\r\n".
        '           dataBase="' . $dbDB . '"'."\r\n".
        '           host="' . $hostDB . '"'."\r\n".
        '           user="' . $userDB . '"'."\r\n".
        '           password="' . $passDB . '"'."\r\n".
        '           persistance="true"'."\r\n".
        '           shared="true"'."\r\n".
        '           schema=""'."\r\n".
        '       />'."\r\n".
        '       </profils>'."\r\n".
        '   </dbdefinition>';
        // on ouvre le fichier pour écriture :
        if ( $handle = @fopen($filename,"w") ) {
            if (fwrite($handle, $XMLconn) === FALSE) {
                trigger_error('Cannot write into ' . $filename, E_USER_ERROR);
            }
            fclose($handle);
            return true;
        }else{
            return false;
        }
    }


    /**
    * Prepare installation, launch sql script needed during installation
    */
    function verifyConnection ($userDB, $passDB, $hostDB, $dbDB, $typeDB) {
        // Search each module install file
        return $ct = CopixDBFactory::testConnection ($typeDB, $dbDB, $hostDB, $userDB, $passDB,'') ;
    }

    /**
    * install
    *
    * Install the database, execute all the module SQL script
    */
    function installAll () {
        $arTemp = $this->getModules ();
        //build an array
        $arModules = array ();
        foreach ($arTemp as $module){
            $arModules[] = $module->name;
        }
        $arError = CopixModule::install($arModules);

        return $arError;
    }

    /**
    *  get all installable modules and their status (install or not), and depedency
    *  @return array of object
    *  @access private
    */
    function getModules () {
        $toReturn    = array ();
        $arInstalledModule = CopixModule::getList(true);

        //lsit all available modules
        foreach (CopixModule::getList(false) as $name){
            //check they have an xml description file
            if (($temp = CopixModule::getInformations ($name)) !== null) {
                //check if they are installed or not
                if (in_array($temp->name, $arInstalledModule)) {
                    $temp->isInstalled = true;
                }else{
                    $temp->isInstalled = false;
                }
                $toReturn[] = $temp;
            }
        }
        return $toReturn;
    }

    /**
    * Prepare installation, launch sql script needed during installation
    */
    function prepareInstall () {
        // find the current connection type (defined in /plugins/copixDB/profils.definition.xml)
        $pluginDB = & $GLOBALS['COPIX']['COORD']->getPlugin ('copixdb');
        $typeDB   = $pluginDB->config->profils['Select']->driver;

        // Search each module install file
        $scriptName = 'prepareinstall.'.$typeDB.'.sql';
        $file = COPIX_MODULE_PATH . 'install/' . COPIX_INSTALL_DIR . 'scripts/' . $scriptName;
        $ct = CopixDBFactory::getConnection () ;
        $ct->doSQLScript($file);
        //make sure that copixmodule is reset
        CopixModule::reset();
    }

    /**
    * Check if the database is correct
    */
    function checkDatabase (){
        $ct = CopixDBFactory::getConnection ();
        return $ct->isConnected ();
    }

    /**
    * Paramètres de la base de données
    */
    function getCurrentParameters (){
        $pluginDB = & $GLOBALS['COPIX']['COORD']->getPlugin ('copixdb');
        return $pluginDB->config->profils[$pluginDB->config->default];
    }
}
?>