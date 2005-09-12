<?php
/**
* @package	copix
* @subpackage profile
* @version	$Id: admin.desc.php,v 1.3 2005/02/09 08:29:09 gcroes Exp $
* @author	Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
//Affiche la liste des profils connus.
$list  = & new CopixActionZone ('GroupList', array ('TITLE_PAGE'=>CopixI18N::get ('profile.title.list')), array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

//édite un profile.
$edit  = & new CopixAction ('profile|profile', 'getEdit', array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

//ajoute une capacité au profil en cours d'édition
$addCapabilities      = & new CopixAction ('profile|profile', 'doAddCapabilities', array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

//enlève une capacité du profil sélectionné.
$removeCapability   = & new CopixAction ('profile|profile', 'doRemoveCapability', array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

//Création d'un profil
$create      = & new CopixAction ('profile|profile', 'doCreate', array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

//prépare l'édition du profil.
$prepareEdit = & new CopixAction ('profile|profile', 'doPrepareEdit', array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

//liste des utilisateurs.
$userList    = & new CopixAction ('profile|profile', 'getUserList', array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

//listCapacities
$capabilitiesList = & new CopixAction ('profile|profile', 'getCapabilitiesList', array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$capabilitiesKind = & new CopixAction ('profile|profile', 'getCapabilitiesKind', array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

//ajout d'un utilisateur.
$addUser = & new CopixAction ('profile|profile', 'doAddUser', array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

//supression d'un utilisateur.
$removeUser = & new CopixAction ('profile|profile', 'doRemoveUser', array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

$valid   = & new CopixAction ('profile|profile', 'doValid', array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

$remove  = & new CopixAction ('profile|profile', 'doRemove', array ('profile|profile'=>new CapabilityValueOf ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));
$update  = & new CopixAction ('profile|profile', 'doValidFromPost', array ('profile|profile'=>new CapabilityValueOf  ('site', 'siteAdmin', PROFILE_CCV_ADMIN)));

$default = & $list;
?>
