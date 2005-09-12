{*
* choix de l'installation
*}
<form action="{copixurl dest="install|install|getInstallKind"}" method="POST">
<p><input type="radio" name="installType" value="default" />{i18n key="install.defaultInstall.button"}<br />
</p>
<p><input type="radio" name="installType" value="custom" />{i18n key="install.customisedInstall.button"}<br />
</p>

<input type="submit" value="{i18n key=copix:common.buttons.valid}" />

</form>