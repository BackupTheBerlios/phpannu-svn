<?php
/**
* @package	copix
* @subpackage profile
* @version	$Id: default.desc.php,v 1.3 2005/02/09 08:29:09 gcroes Exp $
* @author	Croes G�rald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
//Affiche la liste des profils connus.
$list  = & new CopixActionZone ('GroupList', array (), array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//�dite un profile.
$edit  = & new CopixAction ('Profile', 'getEdit', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//ajoute une capacit� au profil en cours d'�dition
$addCapacities      = & new CopixAction ('Profile', 'doAddCapabilities', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//enl�ve une capacit� du profil s�lectionn�.
$removeCapacity   = & new CopixAction ('Profile', 'doRemoveCapability', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//Cr�ation d'un profil
$create      = & new CopixAction ('Profile', 'doCreate', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//pr�pare l'�dition du profil.
$prepareEdit = & new CopixAction ('Profile', 'doPrepareEdit', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//liste des utilisateurs.
$userList    = & new CopixAction ('Profile', 'getUserList', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//listCapacities
$capacitiesList = & new CopixAction ('Profile', 'getCapacitiesList', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//ajout d'un utilisateur.
$addUser = & new CopixAction ('Profile', 'doAddUser', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//supression d'un utilisateur.
$removeUser = & new CopixAction ('Profile', 'doRemoveUser', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

$valid = & new CopixAction ('Profile', 'doValid', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

$remove  = & new CopixAction ('Profile', 'doRemove', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));
$update  = & new CopixAction ('Profile', 'doValidFromPost', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

$default = & $list;
?>
