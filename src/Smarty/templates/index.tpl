{extends file="layout.tpl"}
{block name="blade"}
<div class="blade-container"> <!-- Blade Navigation Start -->
	<h1 class="page-title">{$page_title}</h1>
</div>
{/block}
{block name="navigation"}{/block}
{block name="tree-navigation"}{/block}
{block name="content"}
<form method="post"><div class="loginscreen">
	<p>Please log in with your Northwestern NetID and password.</p>

	<div class="notification-container">
		<span class="icon-error{if !$error} hidden{/if}"><img src="images/error.png" width="23" height="20" /></span>

		<p class="notification-error">{$error}</p>
	</div>

	<div class="login-container">

		<div class="login-field">
			<input type="text" id="netid" name="netid" /></div>
		<div class="login-field">
			<input type="password" id="password" name="password" /> <a class="btn btn-login" href="#">Login</a></div>

	</div>
	<p class="passwordreminder">Did you
		<a href="https://validate.it.northwestern.edu/idm/user/login.jsp" target="_blank">lose or forget</a> your NetID
		or password?</p></div>
</form>
{/block}
{block name="body-script"}
<script type="text/javascript" src="scripts/index.min.js"></script>
{/block}