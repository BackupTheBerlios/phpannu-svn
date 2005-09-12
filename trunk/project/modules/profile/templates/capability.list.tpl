{if count ($arCapabilityPath) > 0}
   <form action="{copixurl dest="admin|addCapabilities"}" method="POST" name="CapabilitiesList" class="copixForm">
      <input type="hidden" value="{$capability}" name="capability" />
      <table class="copixTable">
       <thead>
       <tr>
        <th>{i18n key="profile.titleTab.path"}</th>
        <th> --- </th>
        {foreach from=$capabilityValues item=CCVValue}
        <th>{$arCapabilitiesCaptions[$CCVValue]}</th>
        {/foreach}
       </tr>
       </thead>
       <tbody>
         {foreach from=$arCapabilityPath item=path}
          <tr>
            {assign var=level value="|"|explode:$path->name_ccpt}
            {assign var=levelNum value=$level|@count}
            <td style="padding-left: {$levelNum*10}px">{$path->description_ccpt}</td>
            <td><input type="radio" name="{$path->name_ccpt|urlencode}" value="" {if $path->currentValue eq null}checked="checked"{/if} /></td>
            {foreach from=$capabilityValues item=CCVValue}
            <td><input type="radio" name="{$path->name_ccpt|urlencode}" value="{$CCVValue}" {if $path->currentValue eq $CCVValue}checked="checked"{/if}/></td>
            {/foreach}
          </tr>
         {/foreach}
         </tbody>
      </table>

      <br /><br />

      <input type="submit" value="{i18n key="copix:common.buttons.ok"}" />
      <input type="button" value="{i18n key="copix:common.buttons.cancel"}" onclick="javascript:document.location = '{copixurl dest="admin|edit"}'" />
   </form>
{else}
   <p>{i18n key="profile.message.noCapEnable"}</p>
   <input type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:document.location = '{copixurl dest="admin|edit"}'" />
{/if}