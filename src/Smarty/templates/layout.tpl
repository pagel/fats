<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>McCormick Faculty Advancement Tracking System</title>
	<meta name="description" content="McCormick Faculty Advancement Tracking System">

	<!-- Stylesheets -->
	<link href="styles/fats.css" rel="stylesheet" />
    <link href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" rel="stylesheet" />

	<!-- JavaScript -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/webfont/1.0.31/webfont.js"></script>
	<!-- Fallback to local copy of JQuery if offline -->
	<script type="text/javascript">window.jQuery || document.write('<script src="scripts/jquery-1.8.3.min.js">\x3C/script>')</script>
	<script type="text/javascript">window.jQuery.ui || document.write('<script src="scripts/jquery-ui-1.9.2.custom.min.js">\x3C/script>')</script>
    {block name="head-script"}{/block}
	<link rel="shortcut icon" href="images/favicon.ico" type="image/vnd.microsoft.icon">
	<link rel="icon" href="images/cog.png" type="image/vnd.microsoft.icon">
</head>
<body{if $isAdmin eq false && $ignoreClass eq true} class="no-navigation"{/if}>
	<div class="banner-container"> <!-- Banner Start -->
		<div class="banner-inner">
			<div class="banner-logo">
				<img src="images/mccormick_logo.png" class="banner-logo-image" alt="Robert R. McCormick School of Engineering and Applied Sciences" />
			</div>
            <h1>
				<span class="identifier"></span>
				<span class="site-title">McCormick Faculty Advancement Tracking System</span>
			</h1>
		</div>
	</div>
	<!-- Banner End -->
<div id="maincontent-holder">
{block name="blade"}
	<div class="blade-container"> <!-- Blade Navigation Start -->
		<h1 class="page-title">{$page_title}</h1>
		<ul class="breadcrumbs">
			<li><a href="/main.php">Home</a> &gt;</li>
			<li class="breadcrumb-last">{$breadcrumb_title}</li>
		</ul>
		<div class="logout-container">
			<p>You are logged in as <strong>{$context_user_netid}</strong> <a href="logout.php">Logout</a><br />
				Your current access level is <strong>{$context_user_accesslevel}</strong><br />
                
            </p>
		</div>
	</div>
	<!-- Blade Navigation End -->
{/block}
<div id="sidebar">
{block name="navigation"}
{if $isAdmin eq true}
	<div class="navigation-container"> <!-- Left Navigation Container Start -->
		<div class="navigation-title">Administration</div>
		<ul>
			<li class="navigation-item"><a href="users.php">Manage Users</a></li>
			<li class="navigation-item"><a href="faculty.php">Manage Faculty</a></li>
			<li class="navigation-item"><a href="options.php">Application Options</a></li>
		</ul>
	</div>
	<!-- Left Navigation Container End -->{/if}
{/block}

{block name="tree-navigation"}
	<div class="folder-container"> <!-- Tree Navigation Container Start -->
		<div class="folder-title">Folders</div>
		<div class="folder-error hidden">{$navigation_error}</div>
		{$navigation}
	</div>
	<!-- Tree Navigation Container End -->
{/block}</div>

	<!-- Content Start -->
<div id="contentcolumn">{block name="content"}{/block}</div>
	<!-- Content End -->
    <div class="clear"></div>
</div>
	<div class="footer-container container"> <!-- Footer Container Start -->
		<div class="footer-logo">
			<img src="images/northwestern_footer_logo.png" class="footer-logo-image" alt="Northwestern University" />
		</div>
        <div class="footer-copyright">
			<ul class="footer-quick-links">
				<li><a href="http://www.mccormick.northwestern.edu/">McCormick Home</a></li>
				<li><a target="_blank" href="http://chinese.mccormick.northwestern.edu/">McCormick Chinese</a></li>
				<li><a href="http://www.mccormick.northwestern.edu/contact.html">Contact Us</a></li>
				<li><a href="http://www.mccormick.northwestern.edu/about/facilities/maps/index.html">Maps</a></li>
				<li><a href="http://www.northwestern.edu/">Northwestern Home</a></li>
				<li><a href="http://aquavite.northwestern.edu/cal/pp/">Northwestern Calendar</a></li>
				<li><a href="http://www.mccormick.northwestern.edu/about/facilities/emergency/index.html">Emergency
					Plan</a></li>
				<li><a href="about/legal_policy/index.html">Legal and Policy Statements</a></li>
			</ul>
			<p>
				&copy; {$smarty.now|date_format:'%Y'} Robert R. McCormick School of Engineering and Applied Science,
				Northwestern University
			</p>
		</div>

		
	</div>
	<!-- Footer Container End -->
	<!-- JavaScript Begin -->
{block name="body-script"}
{/block}
	<!-- JavaScript End -->
</body>
</html>