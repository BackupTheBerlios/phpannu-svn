<?php
/**
* @package   copix
* @subpackage plugins
* @version   $Id: i18n.plugin.datas.php,v 1.6 2005/02/09 08:29:09 gcroes Exp $
* @author   Jouanneau Laurent
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

$i18n_languages = array(
    // code => array (name , default currency code )
    'fr'=>array('fr', 'fr', 'Français', 'EUR'),
    'fr_fr'=>array('fr', 'fr', 'Français', 'EUR'),
    'en'=>array('en', 'en', 'English',  'EUR'),
    'en_en'=>array('en', 'en', 'English',  'EUR'),
    'en_us'=>array('en', 'us', 'English',  'USD')

);

$i18n_alternate_languages_code=array('french'=>'fr', 'english'=>'en');


$i18n_currencies = array(
    // code => array(name, symbol left, symbol right, decimal point, thousands point,
                                        // decimal_places, value, last_updated
    'EUR' => array('Euro',  '', '&euro;',   ',',    ' ',    2,  1.00000000,  '2002-12-09 11:19:14'),
    'FRF' => array('Franc', '', 'FRF',      ',',    ' ',    2,  6.55957000,  '2002-12-09 11:19:14'),
    'USD' => array('USDollars', '$', '',      '.',    ',',    2,  1,  '2002-12-09 11:19:14')

);

?>
