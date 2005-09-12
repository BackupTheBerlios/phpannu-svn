{**
* Select online pages.
*}
<h2><img src="./img/modules/cms/big_publish.png" />Pages disponibles</h2>

{if $popup}
<table>
	<tr>
		<th colspan="2">{i18n key="htmleditor.info.popup"}</th>
	</tr>
	<tr>
		<th>{i18n key="htmleditor.popupwidth"} :</th>
		<td><input name="popupwidth" id="popupwidth" type="text" value="{$width}" /></td>
	</tr>
	<tr>
		<th>{i18n key="htmleditor.popupheight"} :</th>
		<td><input name="popupheight" id="popupheight" type="text" value="{$height}" /></td>
	</tr>
</table>
{/if}

   {if count ($arPublished)}
     {foreach from=$arHeadings item=head}
        {assign var=indice value=$head->id_head}
           {if isset ($arPublished[$indice]) }
           <h3>{$head->caption_head}</h3>
           <div style="height: 60px;width:300px;overflow: auto">
           <table width="200">
           {foreach from=$arPublished[$indice] item=page}
             <tr>
              <td>{$page->title_cmsp}</td>
              <td width="20"><a href="#" onclick="{if $popup}javascript:window.opener.{$editorName}.surroundHTML('<a href=&#34;#&#34; onclick=&#34;window.open(\'index.php?module=cms&action=get&id={$page->id_cmsp}\',\'popup\',\'toolbar=no,scrollbars=yes,height='+document.getElementById('popupheight').value+',width='+document.getElementById('popupwidth').value+'\');&#34; >', '</a>');{else}javascript:window.opener.{$editorName}._doc.execCommand('createlink', false, 'index.php?module=cms&action=get&id={$page->id_cmsp}');{/if}window.close();"><img src="./img/tools/valid.png" alt="{i18n key="copix:common.buttons.select"}" border="0"/></a></td>
             </tr>
           {/foreach}
           </table>
           </div>
        {/if}
     {/foreach}
   {/if}
