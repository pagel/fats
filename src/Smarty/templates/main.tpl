{extends file="layout.tpl"}
{block name="blade"}
<div class="blade-container"> <!-- Blade Navigation Start -->
	<h1 class="page-title">{$page_title}</h1>
	<div class="logout-container">
		<p>You are logged in as <strong>{$context_user_netid}</strong> <a href="logout.php">Logout</a><br />
			Your current access level is <strong>{$context_user_accesslevel}</strong><br />
			
		</p>
	</div>
</div>
<!-- Blade Navigation End -->
{/block}
{block name="tree-navigation"}{/block}
{block name="content"}

<p class="facultyselect">To begin, select a faculty member from the list below and click Load Documents.</p>
<div class="faculty-container">
	<ul class="select-container">
        {foreach $faculty as $f}
			<li class="toggle-selected" data-value="{$f->id}">{$f->name}</li>
        {/foreach}</ul>
</div>
<div class="button-container">
	<a class="btn btn-blue btn-active" data-value="load-documents" href="#">Load Documents</a>
</div>
{/block}
{block name="body-script"}
<script type="text/javascript" src="scripts/main.min.js"></script>
{/block}