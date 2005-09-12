{if count ($capacities) > 0}
<form action="{copixurl dest="profile||addCapacities"}" method="POST" name="CapacitiesList">
   <table>
    <tr>
     <th>{i18n key="profile.titleTab.capability"}</th><th>{i18n key="profile.titleTab.description"}</th><th>{i18n key="profile.titleTab.selection"}</th>
    </tr>
      {foreach from=$capacities item=capacity}
       <tr>
         <td>{$capacity->id_ccap}</td>
         <td>{$capacity->description_ccap}</td>
         <td><input type="checkbox" name="selectedCapacities[]" value="{$capacity->id_ccap}" /></td>
       </tr>
      {/foreach}
   </table>

   <br /><br />

   <input type="submit" value="{i18n key="copix:common.button.add"}" />
   <input type="button" value="{i18n key="copix:common.button.cancel"}" onclick="javascript:document.location = '{copixurl dest="profile||edit"}'" />

</form>

{else}
 <p>{i18n key="profile.message.noCapEnable"}</p>
 <input type="button" value="Retour" onclick="javascript:document.location = '{copixurl dest="profile||edit"}'" />
{/if}
