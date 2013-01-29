{extends file="layout.tpl"}
{block name="tree-navigation"}{/block}
{block name="content"}
<div class="button-container">
	<a id="add" class="btn btn-green btn-active" href="#">Add New Faculty</a>
	<a id="delete" class="btn btn-green btn-inactive" href="#">Delete Selected Faculty</a>
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
				<th><p>NetID</p></th>
			</tr>
		</thead>
		<tbody>
        {foreach $faculty as $f}
			<tr class="faculty{$f->id}">
				<td>
					<input type="radio" name="selectfaculty" class="table-check" value="{$f->id}" /></td>
				<td>{$f->name}</td>
				<td>{$f->netid}</td>
			</tr>
        {/foreach}
		</tbody>
	</table>
</div>
<!-- The dialogs -->
<div class="confirmation-container"></div>
<div class="add-faculty-container hidden">
	<form id="add-faculty-form">
		<p>Type or paste the NetID of the faculty member you would like to add and click Add Faculty.</p>

		<div class="fieldset"><label for="netid">NetID:&nbsp;</label>
			<input type="text" size="20" id="netid" name="netid" /></div>
	</form>
</div>
{/block}
{block name="body-script"}
<script type="text/javascript" src="scripts/faculty.min.js"></script>
{/block}