{if count ($capabilities) > 0}
   <h2>{i18n key="profile.title.chooseKind"}</h2>
   {foreach from=$capabilities item=capability}
      <a href="{copixurl dest="admin|capabilitiesList" capability=$capability->name_ccpb}">{$capability->description_ccpb}</a>
      <br />
   {/foreach}
{else}
   <p>{i18n key="profile.message.noCapEnable"}</p>
   <input type="button" value="{i18n key="copix:common.buttons.back"}" onclick="javascript:document.location = '{copixurl dest="admin|edit"}'" />
{/if}
