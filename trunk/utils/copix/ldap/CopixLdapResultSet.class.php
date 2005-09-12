<?php
class CopixLDAPResultSet {
   /**
   * The connection used to the current search
   */
   var $_ldapConnection    = null;
   /**
   * The search result ID
   */
   var $_ldapSearchResults = null;

   /**
   * the last fetched entry
   * @var resource
   */
   var $_lastEntryID = null;

   /**
   * first entry
   * @var resource
   */
   var $first = null;

   function CopixLDAPResultSet (& $ldapConnection, $id) {
      $this->first = true;
      $this->_ldapConnection    = $ldapConnection;
      $this->_ldapSearchResults = $id;
   }

   /**
   * fetch the result (single line)
   */
   function & fetch () {
      //static $first = true;
      if ($this->first === true){
         $method = 'ldap_first_entry';
         $this->first = false;
         $searchID = $this->_ldapSearchResults;
      }else{
         $method = 'ldap_next_entry';
         $searchID = $this->_lastEntryID;
      }

      if (($this->_lastEntryID = $method ($this->_ldapConnection->getConnectionResource (), $searchID)) === false){
         $res = null;
      }else{
         $res = & new CopixLDAPEntry ($this->_ldapConnection->getConnectionResource (), $this->_lastEntryID);
      }
      return $res;
   }

   /**
   * Count the results of the current search
   */
   function count (){
      return ldap_count_entries ($this->_ldapConnection->getConnectionResource (), $this->_ldapSearchResults);
   }
}
?>