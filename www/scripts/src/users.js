/*!
 * users.js
 *
 * Script methods for user account transactions
 * Project: McCormick Faculty Advancement Tracking System
 * Author: Geoffrey Pagel
 * Licensed under the MIT license
 */
$(document).ready(function () {
    $('input[name="selectuser"]').change(function () {
        $('.btn-inactive').removeClass('btn-inactive').addClass('btn-active');
    });

    // Setup the modal confirmation dialog
    $('.confirmation-container').dialog({
        modal:true,
        width:300,
        height:200,
        autoOpen:false,
        title:'Confirmation Required'
    });

    $('body').on('click', '#delete.btn-active', function (e) {
        var selectedUserId = $('input:radio[name="selectuser"]:checked').val(),
            selectedUserClass = 'tr.user' + selectedUserId,
            selectedUserName = $(selectedUserClass + ' td.name').html();

        // Before sending the delete request, ask the user to confirm the action
        var confirmationMessage = '<p>Are you sure you want to delete the selected user account (' + selectedUserName + ')?</p>';
        $('.confirmation-container').html(confirmationMessage);

        $('.confirmation-container').dialog({
            buttons:{
                'Confirm':function () {
                    $(this).dialog('close');

                    var request = $.ajax({
                        async:false,
                        type:'DELETE',
                        dataType:'json',
                        url:'webservice.php/users/' + selectedUserId,
                        timeout:10000,
                        cache:false
                    });

                    request.done(function (x) {
                        if (x.result == true) {
                            $(selectedUserClass).remove();
                            $('#delete').removeClass('btn-active').addClass('btn-inactive');
                            $('#update').removeClass('btn-active').addClass('btn-inactive');

                            $('div.notification-container p').addClass('notification-success').html('The system successfully deleted the user.');
                        }

                        // If we got a string response, something went wrong
                        if (typeof x.result === 'string') {
                            $('div.notification-container p').addClass('notification-error').html(x.result);
                        }
                    });
                    request.fail(function (x) {
                        $('div.notification-container p').addClass('notification-error').html('The system was unable to delete the user.');
                    });
                },
                'Cancel':function () {
                    $(this).dialog('close');
                }
            }
        });

        $('.confirmation-container').dialog('open');
    });

    $('body').on('click', '#update.btn-active', function (e) {
        var selectedUserId = $('input:radio[name="selectuser"]:checked').val(),
            selectedUserClass = 'tr.user' + selectedUserId,
            selectedUserNetId = $(selectedUserClass + ' td.netid').html(),
            selectedUserName = $(selectedUserClass + ' td.name').html(),
            selectedUserEmail = $(selectedUserClass + ' td.email').html(),
            selectedUserRole = $(selectedUserClass + ' td.role select option:selected').val(),
            selectedUserPermission = $(selectedUserClass + ' td.permission select option:selected').val();

        var request = $.ajax({
            async:false,
            type:'PUT',
            dataType:'json',
            data:{id:selectedUserId, netid:selectedUserNetId, name:selectedUserName, email:selectedUserEmail, role:selectedUserRole, permission:selectedUserPermission},
            url:'webservice.php/users',
            timeout:10000,
            cache:false
        });

        request.done(function (x) {
            if (x.result == true) {
                $('div.notification-container p').addClass('notification-success').html('The system successfully updated the user.');
            }

            // If we got a string response, something went wrong
            if (typeof x.result === 'string') {
                $('div.notification-container p').removeClass('notification-success').addClass('notification-error').html(x.result);
            }
        });
        request.fail(function () {
            $('div.notification-container p').addClass('notification-error').html('The system was unable to update the user.');
        });
    });

    $('#add.btn-active').click(function (e) {
        $('.add-user-container').dialog({
            modal:true,
            width:450,
            autoOpen:false,
            draggable:false,
            title:'Add User',
            buttons:{
                'Add User':function () {
                    var netid = $('.add-user-container #netid').val(),
                        role = $('.add-user-container #accesslevel').find(':selected').val(),
                        permission = $('.add-user-container #permission').find(':selected').val();

                    var request = $.ajax({
                        async:false,
                        type:'POST',
                        dataType:'json',
                        data:{netid:netid, role:role, permission:permission},
                        url:'webservice.php/users',
                        timeout:10000,
                        cache:false
                    });

                    request.done(function (x) {
                        if (x.result == true) {
                            var roles = new Array(),
                                permissions = new Array();

                            $('.notification-container p').addClass('notification-success').html('The system successfully added the user.');

                            // Get roles and permissions from the database to use later
                            var request = $.ajax({
                                async:false,
                                type:'GET',
                                dataType:'json',
                                url:'webservice.php/roles',
                                timeout:10000,
                                cache:false
                            });

                            request.done(function (x) {
                                for (b = 0; b < x.result.length; b++) {
                                    var role = x.result[b];
                                    roles.push({id:role.id, description:role.description});
                                }
                            });

                            var request2 = $.ajax({
                                async:false,
                                type:'GET',
                                dataType:'json',
                                url:'webservice.php/permissions',
                                timeout:10000,
                                cache:false
                            });

                            request2.done(function (x) {
                                for (c = 0; c < x.result.length; c++) {
                                    var permission = x.result[c];
                                    permissions.push({id:permission.id, description:permission.description});
                                }
                            });

                            var request3 = $.ajax({
                                async:false,
                                type:'GET',
                                dataType:'json',
                                url:'webservice.php/users',
                                timeout:10000,
                                cache:false
                            });

                            // Rebuild the users table
                            request3.done(function (x) {
                                var users = '';

                                if (x.result !== null) {
                                    for (var a = 0; a < x.result.length; a++) {
                                        users += '<tr class="user' + x.result[a].id + '">' +
                                            '<td class="id"><input type="radio" name="selectuser" class="table-check" value="' + x.result[a].id + '" /></td>' +
                                            '<td class="name">' + x.result[a].name + '</td>' +
                                            '<td class="netid">' + x.result[a].netid + '</td>' +
                                            '<td class="email">' + x.result[a].email + '</td>' +
                                            '<td class="role"><select name="selectuserrole">';

                                        for (d = 0; d < roles.length; d++) {
                                            var role = roles[d];
                                            users += '<option value="' + role.id + '"';

                                            if (role.id == x.result[a].role) {
                                                users += 'selected="selected"';
                                            }

                                            users += '>' + role.description + '</option>';
                                        }

                                        users += '</select></td>' +
                                            '<td class="permission"><select name="selectuserpermission">';

                                        for (e = 0; e < permissions.length; e++) {
                                            var permission = permissions[e];
                                            users += '<option value="' + permission.id + '"';

                                            if (permission.id == x.result[a].permissions) {
                                                users += 'selected="selected"';
                                            }

                                            users += '>' + permission.description + '</option>';
                                        }

                                        users += '</select></td>' +
                                            '</tr>';
                                    }
                                }
                                else {
                                    users = '<tr><td colspan="4">No users exist in database.</td></tr>';
                                }

                                $('table tbody').html(users);
                            });

                            request3.fail(function (x) {
                                alert('The system was unable to refresh the user list. The page will now reload to attempt to refresh it.');
                                location.reload(true);
                            })
                        }
                        else {
                            $('.notification-container p').addClass('notification-error').html('The system was unable to add the user.');
                        }
                    });
                    request.fail(function () {
                        $('.notification-container p').addClass('notification-error').html('The system was unable to add the user.');
                    });

                    $('.add-user-container #netid').val('');
                    $('.add-user-container #accesslevel').find(':selected').prop('selected', false);
                    $('.add-user-container #permission').find(':selected').prop('selected', false);
                    $(this).addClass('hidden').dialog('close');
                },
                'Cancel':function () {
                    $(this).dialog('close');
                }
            }
        });

        $('.add-user-container').removeClass('hidden').dialog('open');
    });
});