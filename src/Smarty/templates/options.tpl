{extends file="layout.tpl"}
{block name="tree-navigation"}{/block}
{block name="content"}
<p>Check or uncheck the appropriate box to enable or disable a feature and click Save Changes.</p>
<div class="notification-container">
	<i class="icon-error"></i>

	<p></p>
</div>
<form class="optionsform">
	<ul>
		<li>
			<input type="checkbox" name="enablemaintenancemode" id="enablemaintenancemode" value="{$maintenance_mode}"{if $maintenance_mode} checked="checked"{/if} />&nbsp;Enable
			maintenance mode
		</li>
		<li>
			<input type="checkbox" name="enabledebugging" id="enabledebugging" value="{$debug_mode}"{if $debug_mode} checked="checked"{/if} />&nbsp;Enable
			debug messages for developers and administrators
		</li>
		<li>
			<input type="checkbox" name="enabledemomode" id="enabledemomode" value="{$demo_mode}"{if $demo_mode} checked="checked"{/if} />&nbsp;Enable
			demonstration mode to bypass authentication
		</li>
	</ul>
</form>
<div class="button-container">
	<a class="btn btn-green btn-active" href="#">Save Changes</a>
</div>
{/block}
{block name="body-script"}
<script type="text/javascript" src="scripts/options.min.js"></script>
{/block}