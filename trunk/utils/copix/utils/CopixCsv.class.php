<?php
/**
* @package   copix
* @subpackage generaltools
* @version   $Id: CopixCsv.class.php,v 1.8 2005/02/09 08:21:44 gcroes Exp $
* @author   Croes Gérald, Jouanneau Laurent
*           see copix.aston.fr for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class CopixCsv {

    var $fieldSeparator=';';
    var $fieldDelimiter='';
    var $escapeChar='\\';

    function CopixCsv($separator=';',$fieldDelimiter='',$escapeChar='\\' ){
        $this->fieldSeparator = $separator;
        $this->fieldDelimiter = $fieldDelimiter;
        $this->escapeChar = $escapeChar;
    }

    function readFile($file, $classname='', $mapping=null){
        $lignes=file($file);
        foreach($lignes as $k=>$l){
            $lignes[$k]=preg_replace("/\015\012|\015|\012/",'',$l);
        }
        $datas = $this->_parse ($lignes);
        return $this->_doMapping($datas,$classname, $mapping);
    }

    function readContent ($content, $classname='', $mapping=null){
        $lignes = preg_split("/\015\012|\015|\012/",$content); // on remplace les \r (mac), les \n (unix) et les \r\n (windows) par un autre caractère pour découper proprement
        $datas = $this->_parse($lignes);

        return $this->_doMapping($datas,$classname, $mapping);
    }

    function _parse($lignes){
        $datas = array();

        if ($this->fieldDelimiter == ''){
            // pas de délimiteur, on fait une analyse simple du contenu
            foreach ($lignes as $ligne){
                $datas[]=split($this->fieldSeparator,$ligne);
            }
        }else{
            // il y a des délimiteurs de champs : ils peuvent donc s'étaler
            // sur plusieurs lignes, il faut en tenir compte

            $escape='(?<!'.str_replace('\\','\\\\',$this->escapeChar).')'; // assertion arrière pour ne pas prendre en compte les séparateurs échappés
            $delim=$escape.'\\'.$this->fieldDelimiter;
            $sep='\\'.$this->fieldSeparator;
            $regexp='/(^'.$sep.')|(?<=('.$delim.'|'.$sep.'))'.$sep.'(?=('.$delim.'|'.$sep.'|$))/';
            $pasfini=false;
            $currentdata=null;
            foreach($lignes as $ligne){
                $data = preg_split($regexp,$ligne);
                //--- il s'agit d'une ligne qui est la continuation d'une ligne précédente (champs sur plusieurs ligne)
                //    il s'agit toujours du même enregistrement
                if($pasfini){
                    if(count($data) <= 1 && $data[0]==''){
                        // ligne vide
                        $lastfield = array_pop($currentdata);
                        $lastfield.="\n";
                        array_push($currentdata,$lastfield);
                    }else{
                        $lastfield = array_pop($currentdata);
                        $lastfield .= array_shift($data);
                        array_push($currentdata,$lastfield);
                        $currentdata = array_merge($currentdata,$data);
                        $lastfield=end($data);
                    }
                    $fin = substr( $lastfield, - sizeof($this->fieldDelimiter) );
                    if($fin == $this->fieldDelimiter){
                        $pasfini=false;
                        $this->_nettoieDelimiteur($currentdata);
                        $datas[]=$currentdata;
                    }

                    //--- il s'agit d'un nouvel enregistrement
                }else{
                    if(count($data) <= 1 && $data[0] == '')
                    continue;

                    $lastfield = end($data);
                    $fin = substr( $lastfield, - sizeof($this->fieldDelimiter) );
                    // test si il y a un délimiteur à la fin de la ligne (dernier champs)
                    if($fin != $this->fieldDelimiter){
                        // y en a pas -> le champs continu sur la ligne suivante
                        $currentdata = $data;
                        $pasfini = true;
                    }else{
                        // y en a un, ok enregistrement complet
                        $this->_nettoieDelimiteur($data);
                        $datas[]=$data;
                    }
                }
            }
        }

        return $datas;
    }

    function _nettoieDelimiteur(&$champs){
        foreach($champs as $k=>$v){
            if($v != ''){
                $champs[$k]=substr($v,1,-1);
                $champs[$k]=str_replace('\\'.$this->fieldDelimiter, $this->fieldDelimiter, $champs[$k]);
            }
        }
    }

    function _doMapping($datas, $classname, $mapping){
        if($classname == ''){
            return $datas;
        }
        if($mapping === null){
            $mapping =  array_keys(get_class_vars ( $classname));
        }

        $result=array();
        foreach($datas as $dt){
            $obj= new $classname();
            foreach($dt as $k=>$v){
                $prop=$mapping[$k];

                $obj->$prop = $v;
            }
            $result[]= $obj;
        }
        return $result;
    }
}
?>