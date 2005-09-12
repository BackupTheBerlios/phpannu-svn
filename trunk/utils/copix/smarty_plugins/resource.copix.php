<?php
/**
* @package   copix
* @subpackage SmartyPlugins
* @version   $Id: resource.copix.php,v 1.2 2005/02/09 08:21:44 gcroes Exp $
* @author   Daspet Eric
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/




/*
* Smarty plugin
* -------------------------------------------------------------
* File:     resource.copix.php
* Type:     resource
* Name:     copix
* Purpose:  Fetches templates from copix file selector
* -------------------------------------------------------------
*/
function smarty_resource_copix_source($tpl_name, &$tpl_source, &$smarty)
{
    // do database call here to fetch your template,
    // populating $tpl_source
	$file = $GLOBALS['COPIX']['COORD']->extractFilePath ($tpl_name, COPIX_TEMPLATES_DIR) ;
	if ($file && $fp = fopen($file, 'r')) {
		$tpl_source = fread($fp, filesize($file)) ;
		return true;
	} else {
        return false;
    }
}

function smarty_resource_copix_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
    // do database call here to populate $tpl_timestamp.
	$file = $GLOBALS['COPIX']['COORD']->extractFilePath ($tpl_name, COPIX_TEMPLATES_DIR) ;
	$tpl_timestamp = filemtime($file) ;
	return true ;
}

function smarty_resource_copix_secure($tpl_name, &$smarty)
{
    // assume all templates are secure
    return true;
}

function smarty_resource_copix_trusted($tpl_name, &$smarty)
{
    // not used for templates
}
?>