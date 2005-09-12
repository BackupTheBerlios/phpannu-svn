<?php
/**
* @package   copix
* @subpackage dbtools
* @version   $Id: CopixDbConnection.oci8.class.php,v 1.11.2.2 2005/08/17 20:06:54 laurentj Exp $
* @author   Croes Gérald, Bertrand Yan
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 *
 * @package copix
 * @subpackage dbtools
 */
class CopixDBConnectionOci8 extends CopixDBConnection {
   function _connect (){
      $funcconnect= ($this->profil->persistent? 'ociplogon':'ocilogon');

      return @$funcconnect ($this->profil->user, $this->profil->password , $this->profil->dbname);
   }

   function _disconnect (){
      return ocilogoff ($this->_connection);
   }

   function &_doQuery ($queryString){
      if (strlen($queryString) > 3900) {
         if (preg_match('/^\s*insert/i',$queryString)) {
            $queryString = preg_replace("/,(\s*)''(\s*)/U",",$1_double___getMeMadIfYouHaveThisValueInHere_$2",$queryString);
            preg_match_all("|'(.*)[^']'{1}\s*[,\)]|Us",$queryString,$matches);
            $i = 0;
            $j = 0;
            while ($matches[1][$j]) {
               $toReplace = substr($matches[0][$j],0, strlen($matches[0][$j]) -1);
               $string = substr($matches[0][$j],1, strlen($matches[0][$j]) -3);
               if (strlen($string) > 3900) {
                  $arReplace[$i] = $string;
                  $queryString = str_replace($toReplace,':v_'.$i,$queryString);
                  $i++;
               }
               $j++;
            }
            $queryString = str_replace("_double___getMeMadIfYouHaveThisValueInHere_","''",$queryString);
         }elseif (preg_match('/^\s*update/i',$queryString)) {
            //on récupère pour analyse tout ce qui se trouve avant le where
            if (($pos = strrpos($queryString,'where')) != false) {
               $beforeWhere = substr($queryString,0,$pos);
            }else{
               $beforeWhere = $queryString;
            }
            //parcoure de la chaine pour trouver les chaine contenant les champs mis à jour
            $length   = strlen($beforeWhere);
            $beginPos = null;
            $endPos   = null;
            $matches  = array ();
            for ($j=0; $j<$length; $j++){
               if (($beforeWhere[$j] === "'") && ($beforeWhere[$j-1] !== "'") && ($beforeWhere[$j+1] !== "'")){
                     if ($beginPos == null) {
                        $beginPos  = $j;
                     }elseif($endPos == null){
                        $endPos    = $j;
                     }else{
                        $matches[] = substr($beforeWhere,$beginPos,($endPos-$beginPos+1));
                        $beginPos  = $j;
                        $endPos    = null;
                     }
               }
            }
            if (($beginPos != null) && ($endPos != null)) {
                  $matches[] = substr($beforeWhere,$beginPos,($endPos-$beginPos+1));
            }
            $i = 0;
            foreach ($matches as $elem){
               $toReplace = substr($elem,0, strlen($elem));
               $string = substr($elem,1, strlen($elem) -2);
               if (strlen($string) > 3900) {
                  $arReplace[$i] = $string;
                  $queryString = str_replace($toReplace,':v_'.$i,$queryString);
                  $i++;
               }
            }
         }
      }
      $stmt = ociparse($this->_connection, $queryString);

      if (is_array($arReplace)) {
         foreach ($arReplace as $key=>$elem){
            $elem = str_replace("_double___getMeMadIfYouHaveThisValueInHere_","''",$elem);
            $elem = $this->_unquote ($elem);
            OCIBindByName($stmt,":v_".$key, $elem,-1);
         }
      }

      if($stmt && ociexecute($stmt)){
         $rs= & new CopixDbResultSetOci8($stmt);
         $rs->_connector = &$this;
      }else{
         $rs = false;
      }
      return $rs;
   }

   function & _doLimitQuery ($queryString, $offset, $number){
      if($stmt && ociexecute($stmt)){
         // dirty hack for Oracle, waiting for oci_fetch_all of PHP 5
         $rs= & new CopixDbResultSetOci8($stmt);
         $rs->_connector = &$this;
         $rs->fetchTo($offset);
         $rs->setFetchCount($number);
      }else{
         $rs = false;
      }
      return $rs;
   }

   function getErrorMessage(){
      if($err= ocierror($this->_connection)){
         return $err['message'];
      }else
         return false;
   }

   function getErrorCode(){
       if($err= ocierror($this->_connection)){
         return $err['code'];
      }else
         return false;
   }

   function lastId ($sequenceName) {
      $query = 'select '.$sequenceName.'.nextVal from dual';
      $rs     = $this->_doQuery ($query);
      $result = $rs->fetch ();
      return $result->NEXTVAL;
   }


   function affectedRows($ressource = null){
      if($ressource !== null && get_class($ressource) == 'CopixDbResultSetOci8' )
          return ocirowcount($ressource->_idResult);
      else return -1;
   }

   function _quote($text){
      return str_replace("'","''",$text);
   }

   function _unquote ($text){
      return str_replace("''","'",$text);
   }

   function begin (){
      return null;
   }

   function commit (){
      return ocicommit($this->_connection);
   }

   function rollBack (){
      return ocirollback($this->_connection);
   }
}
?>
