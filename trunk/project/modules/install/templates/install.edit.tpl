{*
* formulaire d'édition de la DB.
*}
<h2>{i18n key="install.title.databaseParameters"}</h2>

{if $databaseNotOk}
<p style="color:red;"><strong>{i18n key=install.messages.databaseNotOk}</strong></p>
{/if}

{if not $configurationFileWritable or not $copixdbok}
<form action="{copixurl dest="install|install|validConnection"}" method="post" name="" class="copixForm">
    {if !$configurationFileWritable}
    <p style="color:red;">{i18n key=install.error.configurationFileNotWritable filePath=$configurationFilePath}</p>
    {/if}
    {if !$copixdbok}
    <p style="color:red;">{i18n key="install.copixdb.disabled"}</p>
    {/if}

    <p style="color:red;">{i18n key="install.messages.changeAndRetry"}</p>
    <p>
    <input type="hidden" id="configurerbd" value="no" />
    <input type="button" onclick="location.href=location.href" value="{i18n key="install.button.retry"}"/>
    <input type="submit" value="{i18n key="install.button.nodatabase"}" /></p>
</form>
{else}

<form action="{copixurl dest="install|install|validConnection"}" method="post" name="" class="copixForm">
<p><label for="configurerbd">{i18n key="install.chkb.configurerbd"}</label>
  <input type="checkbox" id="configurerbd" name="configurerbd" value="oui"
  checked="checked"
    onclick="var t=document.getElementById('configvalue').style; if(this.checked) t.display='block'; else t.display='none';"
  /></p>

 <div id="configvalue">

   <fieldset>
   <legend>{i18n key="install.databaseLocation"}</legend>
   <table>
      <tr>
       <th>{i18n key="install.host_conn"}</th>
       <td><input type="text" name="host_db" value="{$currentParameters->host}" /></td>
      </tr>
      <tr>
       <th>{i18n key="install.database_conn"}</th>
       <td><input type="text" name="db_db" value="{$currentParameters->dbname}" /></td>
      </tr>
  </table>
  </fieldset>

  <fieldset>
   <legend>{i18n key="install.databaseUserLogin"}</legend>
   <table>
      <tr>
        <th>{i18n key="install.user_conn"}</th>
        <td><input type="text" name="user_db" value="{$currentParameters->user}" /></td>
      </tr>
      <tr>
        <th>{i18n key="install.pass_conn"}</th>
        <td><input type="password" name="pass_db" value="{$currentParameters->password}" /></td>
      </tr>
   </table>
   </fieldset>

  <fieldset>
  <table>
        <tr>
            <th>{i18n key="install.type_conn"}</th>
            <td>
                <select name="type_db">
                    {foreach from=$arType item=curType}
                        <option value="{$curType}">{$curType}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
   </table>
   </fieldset>



</div>



   <p><input type="submit" value="{i18n key="copix:common.buttons.ok"}" /></p>
</form>
<h2>{i18n key=install.title.help}</h2>
<div style="border: 2px slashed">
<h3>{i18n key="install.title.parametersLocation"}</h3>
<p>{i18n key=install.messages.parametersLocation}</p>

<h3>{i18n key=install.title.easyPHPExample}</h3>
<p>{i18n key=install.messages.easyPHPExample}</p>

<h3>{i18n key=install.title.stillInTrouble}</h3>
<p>{i18n key=install.messages.stillInTrouble}</p>
</div>

{/if}
