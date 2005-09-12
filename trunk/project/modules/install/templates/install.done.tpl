{*
* formulaire d'édition de la DB.
*}
<p>
{if $report}
   {i18n key="install.error.installerror"} :
    <ul class="copixError">
   {foreach from=$report item=error}
        <li>{$error}</li>
    {/foreach}
    </ul>
    {if $newInstall}
    <a href="{copixurl dest="install|install|getInstallKind"}">{i18n key="install.result.linkback"}</a>
    {else}
    <a href="{copixurl dest="install|install|manageModules"}">{i18n key="install.result.linkback"}</a>
   {/if}
{else}
    {i18n key="install.result.installok"}<br />
    <a href="{copixurl}index.php">{i18n key="install.result.linkhome"}</a>
{/if}
</p>
