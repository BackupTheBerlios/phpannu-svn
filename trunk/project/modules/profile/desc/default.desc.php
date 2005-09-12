<?php
/**
* @package	copix
* @subpackage profile
* @version	$Id: default.desc.php,v 1.3 2005/02/09 08:29:09 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
//Affiche la liste des profils connus.
$list  = & new CopixActionZone ('GroupList', array (), array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//édite un profile.
$edit  = & new CopixAction ('Profile', 'getEdit', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//ajoute une capacité au profil en cours d'édition
$addCapacities      = & new CopixAction ('Profile', 'doAddCapabilities', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//enlève une capacité du profil sélectionné.
$removeCapacity   = & new CopixAction ('Profile', 'doRemoveCapability', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//Création d'un profil
$create      = & new CopixAction ('Profile', 'doCreate', array ('Profile'=>array ('site'=>PROFILE_CCV_ADMIN)));

//prépare l'édition du profil.
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
