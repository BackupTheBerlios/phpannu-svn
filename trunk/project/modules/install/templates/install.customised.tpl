{*
* form to select which module to install
*}
   <h2>{i18n key="install.title.installedModules"}</h2>
   <table class="CopixTable">
    <tr>
     <th>{i18n key=install.titleTab.name}</th>
     <th>{i18n key=install.titleTab.description}</th>
     <th>{i18n key=install.titleTab.technicalName}</th>
     <th>{i18n key=install.titleTab.actions}</th>
    </tr>
      {foreach from=$arModules item=module}
         {if $module->isInstalled}
           <tr {cycle values=",class='alternate'"}>
            <td>{$module->description}</td>
            <td>{$module->longDescription}</td>
            <td>{$module->name}</td>
            <td><a href="{copixurl dest="install|install|installModule" moduleName=$module->name todo="remove"}"><img src="{copixurl}img/tools/delete.png" alt="{i18n key="copix:common.buttons.delete"}" /></a></td>
           </tr>
         {/if}
      {/foreach}
   </table>

   <h2>{i18n key="install.title.InstallableModules"}</h2>
   <table class="CopixTable">
    <tr>
     <th>{i18n key=install.titleTab.name}</th>
     <th>{i18n key=install.titleTab.description}</th>
     <th>{i18n key=install.titleTab.technicalName}</th>
     <th>{i18n key=install.titleTab.actions}</th>
    </tr>
     {foreach from=$arModules item=module}
         {if ! $module->isInstalled}
             <tr  {cycle values=",class='alternate'"}>
              <td>{$module->description}</td>
              <td>{$module->longDescription}</td>
              <td>{$module->name}</td>
              <td><a href="{copixurl dest="install|install|installModule" moduleName=$module->name todo="add"}"><img src="{copixurl}img/tools/add.png" alt="{i18n key="copix:common.buttons.add"}" /></a></td>
             </tr>
         {/if}
      {/foreach}
   </table>

{if $newInstall}
<input type="button" onclick="javascript:window.location='{copixurl dest="install|install|getInstallKind"}'" value="{i18n key="copix:common.buttons.cancel"}" />
{else}
<input type="button" onclick="javascript:window.location='{copixurl dest="install|install|getAdmin"}'" value="{i18n key="copix:common.buttons.cancel"}" />
{/if}