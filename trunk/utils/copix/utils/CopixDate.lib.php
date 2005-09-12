<?php
/**
* @package   copix
* @subpackage generaltools
* @version   $Id: CopixDate.lib.php,v 1.9.4.1 2005/05/11 20:47:53 laurentj Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
* convertit une date format fr au format mysql. (a utiliser avant l'envois vers mysql)
* @param   string  $DateToConvert  La date a convertir.
* @param   string  $SplitChar      le caractère de séparation utilisé dans $DateToConvert. (facultatif)
* @return  string  La chaine de caractère compréhensible par mysql.
*/
function dateFrToMySQL ( $DateToConvert, $SplitChar='/'){
   return dateFrToBd($DateToConvert, $SplitChar);
}
/**
* Checks a given date (from year, month and day) (says if it exists)
* @param mixed $year the year
* @param mixed $month the month
* @param mixed $day the day
* @return boolean
*/
function checkGivenDate($year, $month, $day) {
    if ($year < 0 || $year > 9999) {
        return false;
    }
    if (!checkdate($month,$day,$year)) {
        return false;
    }
    return true;
}

/**
 * convertit une date format fr au format mysql. (a utiliser avant l'envois vers mysql)
 * @param  string   $DateToConvert  La date a convertir.
 * @param  string   $SplitChar      le caractère de séparation utilisé dans $DateToConvert. (facultatif)
 * @return string   La chaine de caractère compréhensible par mysql.
 */
function dateFrToBd ($DateToConvert, $SplitChar='/'){
   if ($DateToConvert == ''){
      return '';
   }
   $tmp = explode ($SplitChar, $DateToConvert);
   return $tmp[2].'-'.$tmp[1].'-'.$tmp[0];
}
/**
 * convertit une date format mysql au format fr. (a utiliser a la réception des données MySql)
 * @param  string   $DateToConvert  La date a convertir.
 * @param  string   $FrSplitChar     le caractère de séparation utilisé dans le format français (facultatif)
 * @return string   La chaine de caractère compréhensible par mysql.
 */
function dateMySQLToFr ($DateToConvert, $FrSplitChar='/'){
   return dateBdToFr($DateToConvert, $FrSplitChar);
}
/**
 * convertit une date format mysql au format fr. (a utiliser a la réception des données d'un sgbd)
 * @param  string   $DateToConvert   La date a convertir.
 * @param  string   $FrSplitChar     le caractère de séparation utilisé dans le format français (facultatif)
 * @param  string   $BdSplitChar     le caractère de séparation utilisé dans le format base de donnée (facultatif)
 * @return string   La chaine de caractère compréhensible par mysql.
 */
function dateBdToFr ( $DateToConvert, $FrSplitChars='/', $BdSplitChar='-'){
   if ($DateToConvert == ''){
      return '';
   }
   $tmp = substr ($DateToConvert, 0, 10);
   $tmp = explode ($BdSplitChar, $tmp);
   return $tmp[2].$FrSplitChars.$tmp[1].$FrSplitChars.$tmp[0];
}

/**
 * Calcul le laps de temps écoulé entre deux dates.
 * @param   string  $DteMin     la date a soustraire de DteMax Chaine au format Fr jj/mm/aaaa.
 * @param   string  $DteMax     la date d'ou soustraire DteMin Chaine au format Fr jj/mm/aaaa.
 * @param   string  $SplitChar  le caractere séparateur utilisé dans les dates (par defaut : /)
 * @return integer  Positif Max > Min, Negatif Max < Min, 0 Max = Min.
 */
function timeBetween ($DteMin, $DteMax, $SplitChar='/'){
   $MinTable = explode ($SplitChar, $DteMin);
   $MaxTable = explode ($SplitChar, $DteMax);
   $Between = mktime (0,0,0,$MaxTable[1], $MaxTable[0], $MaxTable[2]) - mktime (0,0,0,$MinTable[1], $MinTable[0], $MinTable[2]);
   return $Between;
}

/**
 * Ajoute un nombre de jours/mois/années à une date et retourne la nouvelle date obtenue.
 * @param  string    $ToDate    La date que l'on va incrémenter. Format Fr.
 * @param  integer   $Day       le nombre de jours à ajouter.
 * @param  integer   $Month     le nombre de mois a ajouter.
 * @param  integer   $year      le nombre d'années à ajouter.
 * @param   string  $SplitChar  le caractere séparateur utilisé dans les dates (par defaut : /)
 * @return string   La date modifiée. Format fr jj-mm-aaaa.
 */
function addToDate ($ToDate, $Day, $Month=0, $Year=0, $SplitChar='/') {
   $TblToDate = explode ($SplitChar, $ToDate);//Tableau avec les valeurs actuelles.
   $BeforeTime = mktime (0, 0, 0, $TblToDate[1], $TblToDate[0], $TblToDate[2]);//Création d'une marque temps avec l'ancienne date.
   $NewValue = $BeforeTime + mktime (0, 0, 0, $Month, $Day, $Year);
   return date('d'.$SplitChar.'m'.$SplitChar.'Y', $NewValue);//Reconversion de la valeur en format date.
}

class CopixDateTime{
    var $day;
    var $month;
    var $year;
    var $hour;
    var $minute;
    var $second;

    var $defaultFormat = 21; // une des valeurs _DT/DFORMAT

    var $FR_DFORMAT=0;
    var $BD_DFORMAT=1;
    var $EN_DFORMAT=2;
    var $FR_DTFORMAT=20;
    var $BD_DTFORMAT=21;
    var $EN_DTFORMAT=22;

    var $ISO8601_FORMAT=40;


    function CopixDateTime($year=0, $month=0, $day=0, $hour=0, $minute=0, $second=0){
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;

    }


    function toString($format=-1){
        if($format==-1)
            $format = $this->defaultFormat;
        $str='';
        switch($format){
           case $this->FR_DFORMAT:
               $str = sprintf('%02d/%02d/%04d', $this->day, $this->month, $this->year);
               break;
           case $this->EN_DFORMAT:
               $str = sprintf('%02d/%02d/%04d', $this->month, $this->day, $this->year);
               break;
           case $this->BD_DFORMAT:
               $str = sprintf('%04d-%02d-%04d', $this->year, $this->month, $this->day);
               break;
           case $this->FR_DTFORMAT:
               $str = sprintf('%02d/%02d/%04d  %02dh%02dm%02ds', $this->day, $this->month, $this->year, $this->hour, $this->minute, $this->second);
               break;
           case $this->BD_DTFORMAT:
               $str = sprintf('%04d-%02d-%04d %02d:%02d:%02d', $this->year, $this->month, $this->day, $this->hour, $this->minute, $this->second);
               break;
           case $this->EN_DTFORMAT:
               $str = sprintf('%02d/%02d/%04d  %02d:%02d:%02d', $this->month, $this->day, $this->year, $this->hour, $this->minute, $this->second);
               break;
           case $this->ISO8601_FORMAT:
               $str = sprintf('%04d%02d%02dT%02d:%02d:%02d', $this->year, $this->month, $this->day, $this->hour, $this->minute, $this->second);
               break;
        }
       return $str;
    }

    function setFromString($str,$format=-1){
        if($format==-1)
            $format = $this->defaultFormat;
        $this->year = 0;
        $this->month = 0;
        $this->day = 0;
        $this->hour = 0;
        $this->minute = 0;
        $this->second = 0;
        $ok=false;
        switch($format){
           case $this->FR_DFORMAT:
               if($ok=preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $str, $match)){
                    $this->year = $match[3];
                    $this->month = $match[2];
                    $this->day = $match[1];
               }
               break;
           case $this->BD_DFORMAT:
               if($ok=preg_match('/^(\d{4})\-(\d{2})\-(\d{2})$/', $str, $match)){
                    $this->year = $match[1];
                    $this->month = $match[2];
                    $this->day = $match[3];
               }
               break;
           case $this->EN_DFORMAT:
               if($ok=preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $str, $match)){
                    $this->year = $match[3];
                    $this->month = $match[1];
                    $this->day = $match[2];
               }
               break;
           case $this->FR_DTFORMAT:
               if($ok=preg_match('/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2})h(\d{2})m(\d{2})s$/', $str, $match)){
                    $this->year = $match[3];
                    $this->month = $match[2];
                    $this->day = $match[1];
                    $this->hour = $match[4];
                    $this->minute = $match[5];
                    $this->second = $match[6];
               }
               break;
           case $this->BD_DTFORMAT:
               if($ok=preg_match('/^(\d{4})\-(\d{2})\-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $str, $match)){
                    $this->year = $match[1];
                    $this->month = $match[2];
                    $this->day = $match[3];
                    $this->hour = $match[4];
                    $this->minute = $match[5];
                    $this->second = $match[6];
               }
               break;
           case $this->EN_DTFORMAT:
               if($ok=preg_match('/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}):(\d{2})$/', $str, $match)){
                    $this->year = $match[3];
                    $this->month = $match[1];
                    $this->day = $match[2];
                    $this->hour = $match[4];
                    $this->minute = $match[5];
                    $this->second = $match[6];
               }
               break;
           case $this->ISO8601_FORMAT:
               if($ok=preg_match('/^(\d{4})(\d{2})(\d{2})T(\d{2}):(\d{2}):(\d{2})$/', $str, $match)){
                    $this->year = $match[1];
                    $this->month = $match[2];
                    $this->day = $match[3];
                    $this->hour = $match[4];
                    $this->minute = $match[5];
                    $this->second = $match[6];
               }
               break;
        }
        return $ok;
    }

}



?>