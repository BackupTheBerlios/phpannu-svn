<?php
/**
* @package	copix
* @subpackage copixldap
* @author	Croes Gérald
*           see copix.aston.fr for other contributors.
* @copyright 2001-2004 Aston S.A.
* @link		http://copix.aston.fr
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @ignore
*/
if (!defined ('COPIX_LDAP_PATH'))
   define ('COPIX_LDAP_PATH', dirname (__FILE__).'/');

require_once (COPIX_LDAP_PATH . 'CopixLdapConnection.class.php');
require_once (COPIX_LDAP_PATH . 'CopixLdapResultSet.class.php');
require_once (COPIX_LDAP_PATH . 'CopixLdapProfil.class.php');
require_once (COPIX_LDAP_PATH . 'CopixLdapEntry.class.php');

/**
* @package copix
* @subpackage copixldap
*/
class CopixLdapFactory {
	/**
	* Récupération d'une connection.
	* @static
	* @param string  $named  nom du profil de connection définie dans CopixLdap.plugin.conf.php
	* @return CopixLdapConnection  objet de connection vers l'annuaire ldap
	*/
	function & getConnection ($named = null) {
		if ($named == null) {
			$foundedConnection = & CopixLdapFactory::getConnection (CopixLdapFactory::getDefaultConnectionName ());
            return $foundedConnection;
		}
		$profil = & CopixLdapFactory::_getProfil ($named);

		//peut être partagé ?
		if ($profil->shared){
			$foundedConnection = & CopixLdapFactory::_findConnection ($named);
			if ($foundedConnection === null){
				$foundedConnection = & CopixLdapFactory::_createConnection ($named);
			}
		}else{
			//Ne peut pas être partagé.
			$foundedConnection = & CopixLdapFactory::_createConnection ($named);
		}
        return $foundedConnection;
	}

	/**
	* récupération d'une connection par défaut.
	* @static
	* @return    string  nom de la connection par défaut
	*/
	function getDefaultConnectionName (){
		$pluginLdap = & $GLOBALS['COPIX']['COORD']->getPlugin ('CopixLdap');
		if ($pluginLdap === null){
			trigger_error (CopixI18N::get('copix:copix.error.plugin.unregister','CopixLdap'), E_USER_ERROR);
            return null;
		}
		return $pluginLdap->config->default;
	}

	/* ======================================================================
	*  private
	*/

	/**
	* récupération d'un profil de connection à une base de données.
	* @access private
	* @param string  $named  nom du profil de connection
	* @return    CopixLdapProfil   profil de connection
	*/
	function & _getProfil ($named){
		$pluginLdap = & $GLOBALS['COPIX']['COORD']->getPlugin ('CopixLdap');
		if ($pluginLdap === null){
			trigger_error (CopixI18N::get('copix:copix.error.plugin.unregister', 'CopixLdap'), E_USER_ERROR);
            return $pluginLdap;
		}

		if (isset ($pluginLdap->config->profils[$named])){
		   return $pluginLdap->config->profils[$named];
		}else{
		    trigger_error(CopixI18N::get('copix:copix.ldap.error.profil.unknow', $named),E_USER_ERROR);
            $ret = null;
		    return $ret;
        }
	}

	/**
	* Récupération de la connection dans le pool de connection, à partir du nom du profil.
	* @access private
	* @param string  $named  nom du profil de connection
	* @return CopixLdapConnection  l'objet de connection
	*/
	function & _findConnection ($profilName){
		$profil = & CopixLdapFactory::_getProfil ($profilName);
		if ($profil->shared){
			//connection partagée, on peut retourner celle qui existe.
			if (isset ($GLOBALS['COPIX']['LDAP'][$profilName])){
				return $GLOBALS['COPIX']['LDAP'][$profilName];
			}
		}
		//la connection n'est pas partagée, quoi qu'il arrive, on ne
		// peut pas retourner une connection existante.
		//(On fera confiance au pool de PHP pour cette gestion)
        $ret = null;
		return $ret;
	}

	/**
	* création d'une connection.
	* @access private
	* @param string  $named  nom du profil de connection
	* @return CopixLdapConnection  l'objet de connection
	*/
	function & _createConnection ($profilName){
		$profil = & CopixLdapFactory::_getProfil ($profilName);

		//Création de l'objet
		$obj = & new CopixLdapConnection ();
		if ($profil->shared) {
			$GLOBALS['COPIX']['LDAP'][$profilName] = & $obj;
		}

		if ($GLOBALS['COPIX']['COORD']->getPluginConf ('CopixLdap', 'showLdapQueryEnabled')
   		&& (isset ($_GET['showLdapQuery'])) && ($_GET['showLdapQuery'] == '1')){
			$obj->_debugQuery = true;
		}

		$obj->connect ($profil);
		return $obj;
	}
}
?>