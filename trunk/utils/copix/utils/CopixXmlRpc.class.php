<?php
/**
* @package   copix
* @subpackage copixtools
* @version   $Id: CopixXmlRpc.class.php,v 1.4 2005/04/05 15:06:09 gcroes Exp $
* @author   Jouanneau Laurent
* @copyright 2001-2005 CopixTeam
* @link      http://copix.aston.fr
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/*
 ========================== CLASSE EXPERIMENTAL ================================
 n'utiliser le support xmlrpc qu'à des fins de test !
 Les spécifications du support xmlrpc dans copix sont succeptibles d'être modifiée
 dans la prochaine version !!
*/

require_once(COPIX_UTILS_PATH.'CopixDate.lib.php');

if(!function_exists('html_entity_decode')){
    function html_entity_decode($str){
        $str = str_replace(array('&lt;','&gt;','&quot;','&#039;') , array('<','>','"',"'"),$str);
        return str_replace('&amp;','&',$str);

    }
}

/**
 * objet permettant d'encoder/décoder des request/responses XMl-RPC
 * pour les specs, voir http://www.xmlrpc.com/spec
 */
class CopixXmlRpc {
    /**
     *
     * @param
     * @return
     */
    function decodeRequest($xmlcontent){
        $simplexml = new CopixSimpleXml();
        if (!($xml = $simplexml->parse($xmlcontent))){
            $simplexml->raiseError ();
        }

        $methodname = $xml->METHODNAME->content();
        if(isset($xml->PARAMS)){
            if(isset($xml->PARAMS->PARAM)){
                $listparam = is_array($xml->PARAMS->PARAM)?$xml->PARAMS->PARAM:array($xml->PARAMS->PARAM);
                $params = array();
                foreach($listparam as $param){
                    if(isset($param->VALUE)){
                        $params[] = CopixXmlRpc::_decodeValue($param->VALUE);
                    }
                }
            }
        }

        return array($methodname, $params);
    }

    /**
     *
     * @param
     * @return
     */
    function encodeRequest($methodname, $params){
          $request =  '<?xml version="1.0"?>
<methodCall><methodName>'.htmlspecialchars($methodname).'</methodName><params>';
           foreach($params as $param){
               $request.= '<param>'.CopixXmlRpc::_encodeValue($param).'</param>';
           }

        $request.='</params></methodCall>';
        return $request;

    }

    /**
     *
     * @param
     * @return
     */
    function decodeResponse($xmlcontent){
        $simplexml = new CopixSimpleXml();
        if (!($xml = $simplexml->parse($xmlcontent))){
           $simplexml->raiseError ();
        }
        $response=array();
        if(isset($xml->PARAMS)){
            if(isset($xml->PARAMS->PARAM)){
                $listparam = is_array($xml->PARAMS->PARAM)?$xml->PARAMS->PARAM:array($xml->PARAMS->PARAM);
                $params = array();
                foreach($listparam as $param){
                    if(isset($param->VALUE)){
                        $params[] = CopixXmlRpc::_decodeValue($param->VALUE);
                    }
                }
                $response[0] = true;
                $response[1]=$params;
            }
        }else if(isset($xml->FAULT)){
            $response[0] = false;
            if(isset($xml->FAULT->VALUE))
                $response[1] = CopixXmlRpc::_decodeValue($xml->FAULT->VALUE);
            else
                $response[1] = null;
        }

        return $response;
    }

    /**
     *
     * @param
     * @return
     */
    function encodeResponse($params){
        return '<?xml version="1.0"?>
<methodResponse><params><param>'.CopixXmlRpc::_encodeValue($params).'</param></params></methodResponse>';
    }

    /**
     *
     * @param
     * @return
     */
    function encodeFaultResponse($code, $message){
        return '<?xml version="1.0"?>
<methodResponse><fault><value><struct>
<member><name>faultCode</name><value><int>'.intval($code).'</int></value></member>
<member><name>faultString</name><value><string>'.htmlspecialchars($message).'</string></value></member>
</struct></value></fault></methodResponse>';
    }

    /**
     *
     * @param
     * @return
     * @access private
     */
    function _decodeValue($valuetag){
        $childs= $valuetag->childs();
        $value = null;
        if(count($childs)){
            switch($childs[0]->name()){
                case 'I4':
                case 'INT':
                    $value= intval($childs[0]->content());
                    break;
                case 'DOUBLE':
                    $value= doubleval($childs[0]->content());
                    break;
                case 'STRING':
                    $value= html_entity_decode($childs[0]->content());
                    break;
                case 'BOOLEAN':
                    $value= intval($childs[0]->content())?true:false;
                    break;
                case 'ARRAY':
                    $value=array();
                    if(isset($childs[0]->DATA->VALUE)){
                        $listvalue = is_array($childs[0]->DATA->VALUE)?$childs[0]->DATA->VALUE:array($childs[0]->DATA->VALUE);
                        foreach($listvalue as $val){
                           $value[] = CopixXmlRpc::_decodeValue($val);
                        }
                    }
                    break;
                case 'STRUCT':
                    $value=array();
                    if(isset($childs[0]->MEMBER)){
                        $listvalue = is_array($childs[0]->MEMBER)?$childs[0]->MEMBER:array($childs[0]->MEMBER);
                        foreach($listvalue as $val){
                           if(isset($val->NAME) && isset($val->VALUE)){
                               $value[$val->NAME->content()] = CopixXmlRpc::_decodeValue($val->VALUE);
                           }
                        }
                    }
                    break;
                case 'DATETIME.ISO8601':
                    $value = new CopixDateTime();
                    $value->setFromString($childs[0]->content(), $value->ISO8601_FORMAT);
                    break;
                case 'BASE64':
                    $value = new CopixBinary();
                    $value ->setFromBase64String($childs[0]->content());
                    break;
            }

        }else{
            $value = $valuetag->content();
        }
        return $value;
    }

    /**
     *
     * @param
     * @return
     * @access private
     */
    function _encodeValue($value){
        $response='<value>';
        if(is_array($value)){

            $isArray = true;
            $datas = array();
            $structkeys = array();
            foreach($value as $key => $val){
                if(!is_numeric($key))
                    $isArray=false;

                $structkeys[]='<name>'.$key.'</name>';
                $datas[]=CopixXmlRpc::_encodeValue($val);
            }

            if($isArray){
                $response .= '<array><data>'.implode(' ',$datas).'</data></array>';
            }else{
                $response .= '<struct>';
                foreach($datas as $k=>$v){
                    $response.='<member>'.$structkeys[$k].$v.'</member>';
                }
                $response .= '</struct>';
            }
        }else if(is_bool($value)){
            $response .= '<boolean>'.($value?1:0).'</boolean>';
        }else if(is_int($value)){
            $response .= '<int>'.intval($value).'</int>';
        }else if(is_string($value)){
            $response .= '<string>'.htmlspecialchars($value).'</string>';
        }else if(is_float($value) ){
            $response .= '<double>'.doubleval($value).'</double>';
        }else if(is_object($value)){
            switch(get_class($value)){
                case 'copixdatetime':
                    $response .= '<dateTime.iso8601>'.$value->toString($value->ISO8601_FORMAT).'</dateTime.iso8601>';
                    break;
                case 'copixbinary':
                    $response .= '<base64>'.$value->toBase64String().'</base64>';
                    break;
            }
        }
        return $response.'</value>';
    }
}


class Copixbinary  {
    var $data;

    function toBase64String(){
        return base64_encode($this->data);
    }

    function setFromBase64String($string){
        $this->data = base64_decode($string);
    }
}
?>