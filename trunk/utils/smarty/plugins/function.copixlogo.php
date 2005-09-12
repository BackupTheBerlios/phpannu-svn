<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     copixlogo
 * Version:  1
 * Date:     May 21, 2002
 * Author:   Gérald Croes
 * input: type : big   -> the big one
 *               small -> simply made with Copix, http://copix.aston.fr
 *        default is small
 * Examples: {copixlogo}
 * Simply output the made with Copix Logo
 * -------------------------------------------------------------
 */
function smarty_function_copixlogo($params, &$smarty)
{
    $cr = "\n";
    extract($params);
    if (empty($type) || $type == 'small') {
       $out = '<!-- made with Copix, http://copix.aston.fr-->'.$cr;
    }else{
      $out = '<!-- made with'.$cr;
      $out .= '    ______   ____     ______   _   _      _'.$cr;
      $out .= '   / ____/  /    \   /  __  \ / \   \    / '.$cr;
      $out .= '  / /      |  --  |  |    | | \_/  \ \  / /'.$cr;
      $out .= ' / /       | |  | |  | |_ __/  _    \ \/ / '.$cr;
      $out .= ' \ \____   |  __| |  | |      | |    \ \/  '.$cr;
      $out .= '  \_____\   \____/   |_|      |_|    / /\  '.$cr;
      $out .= '                                    / /\ \ '.$cr;
      $out .= '___________________________________/_/  \_\___'.$cr;
      $out .= 'Open Source Framework for PHP                 |'.$cr;
      $out .= '-----------------------------------------------'.$cr;
      $out .= '                          http://copix.aston.fr'.$cr;
      $out .= '-->'.$cr;
    }
    return $out;
}
?>
