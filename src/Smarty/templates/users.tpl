{extends file="layout.tpl"}
{block name="tree-navigation"}{/block}
{block name="content"}
<div class="button-container">
	<a id="add" class="btn btn-green btn-active" href="#">Add New User</a>
	<a id="delete" class="btn btn-green btn-inactive" href="#">Delete Selected User</a>
	<a id="update" class="btn btn-green btn-inactive" href="#">Update Selected User</a>
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
				<th><p>E-mail Address</p></th>
				<th><p>Role</p></th>
                <th><p>Permission</p></th>
			</tr>
		</thead>
		<tbody>
            {foreach $users as $user}
				<tr class="user{$user->id}">
					<td class="id">
						<input type="radio" name="selectuser" class="table-check" value="{$user->id}" /></td>
					<td class="name">{$user->name}</td>
					<td class="netid">{$user->netid}</td>
					<td class="email">{$user->email}</td>
					<td class="role">
						<select name="selectuserrole">
                            {foreach $roles as $role}
								<option value="{$role['id']}"{if $role['id'] == $user->role} selected="selected"{/if}>{$role['description']}</option>
                            {/foreach}
						</select></td>
					<td class="permission">
						<select name="selectuserpermission">
                            {foreach $permissions as $permission}
								<option value="{$permission['id']}"{if $permission['id'] == $user->permissions} selected="selected"{/if}>{$permission['description']}</option>
                            {/foreach}
						</select></td>
                </tr>
            {/foreach}
		</tbody>
	</table>
</div>
<!-- The dialogs -->
<div class="confirmation-container"></div>
<div class="add-user-container hidden">
	<form id="add-user-form">
		<p>Type or paste the NetID of the user you would like to add, select an access level from the drop-down
			list, then click Add User.</p>

		<div class="fieldset"><label for="netid">NetID:&nbsp;</label>
			<input type="text" size="20" id="netid" name="netid" /></div>
		<div class="fieldset"><label for="accesslevel">Access Level:&nbsp;</label>
			<select name="accesslevel" id="accesslevel">
                {foreach $roles as $role}
					<option value="{$role['id']}">{$role['description']}</option>
                {/foreach}
			</select></div>
		<div class="fieldset"><label for="permission">Permission:&nbsp;</label>
			<select name="permission" id="permission">
                {foreach $permissions as $permission}
					<option value="{$permission['id']}">{$permission['description']}</option>
                {/foreach}
			</select></div>
	</form>
</div>
{/block}
{block name="body-script"}
<script type="text/javascript" src="scripts/users.min.js"></script>
{/block}