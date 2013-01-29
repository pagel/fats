{extends file="layout.tpl"}
{block name="navigation"}
<div class="button-container">
	<a class="btn btn-blue btn-active" href="/main.php">Change Faculty Member</a>
</div>
{if $write eq true || $delete eq true}
<div class="button-container">
	<a id="upload" class="btn btn-green btn-inactive" href="#">Upload Files</a>
</div>{/if}
{/block}
{block name="content"}
<div class="button-container">
	<a id="downloadall" class="btn btn-green btn-active" href="#">Download All Files in All Folders</a>
	<a id="download" class="btn btn-green btn-inactive" href="#">Download Selected Files</a>
    {if $delete eq true}
        <a id="delete" class="btn btn-green btn-inactive" href="#">Delete Selected Files</a>{/if}
</div>
<div class="table-container">
	<div class="notification-container">
		<i class="icon-error"></i>

		<p></p>
	</div>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th><p>Select</p></th>
				<th><p>Name</p></th>
				<th><p>Kind</p></th>
				<th><p>Size</p></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="4">Please select a folder in the navigation pane on the left.</td>
			</tr>
		</tbody>
	</table>
</div>
<!-- The dialogs -->
<div class="confirmation-container"></div>
<div class="file-upload-container hidden">
	<form id="file-upload-form" action="webservice.php/documents" method="post" enctype="multipart/form-data">
		<input size="50" type="file" name="document">
		<input class="btn btn-green btn-active" type="submit" value="Upload File">

		<div class="progress hidden">
			<p>Upload progress:</p>

			<div class="progress-bar"></div>
			<div class="progress-percent">0%</div>
		</div>
	</form>
	<script type="text/javascript" src="scripts/jquery.form.min.js"></script>
	<script type="text/javascript" src="scripts/src/upload.js"></script>
</div>
<div class="file-download-container"></div>
{/block}
{block name="body-script"}
<script type="text/javascript" src="scripts/documents.min.js"></script>
{/block}