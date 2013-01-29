/*!
 * faculty.js
 *
 * Script methods for faculty member transactions
 * Project: McCormick Faculty Advancement Tracking System
 * Author: Geoffrey Pagel
 * Licensed under the MIT license
 */
$(document).ready(function () {
    $('input[name="selectfaculty"]').change(function () {
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
        var selectedFacultyId = $('input:radio[name="selectfaculty"]:checked').val(),
            selectedFacultyClass = 'tr.faculty' + selectedFacultyId;

        // Before sending the delete request, ask the user to confirm the action
        var confirmationMessage = '<p>Are you sure you want to delete the selected faculty member?</p>';
        $('.confirmation-container').html(confirmationMessage);

        $('.confirmation-container').dialog({
            buttons:{
                'Confirm':function () {
                    $(this).dialog('close');

                    var request = $.ajax({
                        async:false,
                        type:'DELETE',
                        dataType:'json',
                        url:'webservice.php/faculty/' + selectedFacultyId,
                        timeout:10000,
                        cache:false
                    });

                    request.done(function (x) {
                        if (x.result == true) {
                            $(selectedFacultyClass).remove();
                            $('#delete').removeClass('btn-active').addClass('btn-inactive');

                            $('div.notification-container p').addClass('notification-success').html('The system successfully deleted the faculty member.');
                        }
                        else {
                            $('div.notification-container p').addClass('notification-error').html('The system was unable to delete the faculty member.');
                        }
                    });
                    request.fail(function () {
                        $('div.notification-container p').addClass('notification-error').html('The system was unable to delete the faculty member.');
                    });
                },
                'Cancel':function () {
                    $(this).dialog('close');
                }
            }
        });

        $('.confirmation-container').dialog('open');
    });

    $('#add.btn-active').click(function (e) {
        $('.add-faculty-container').dialog({
            modal:true,
            autoOpen:false,
            draggable:false,
            title:'Add Faculty',
            buttons:{
                'Add Faculty':function () {
                    var netid = $('.add-faculty-container #netid').val();

                    var request = $.ajax({
                        async:false,
                        type:'POST',
                        dataType:'json',
                        data:{netid:netid},
                        url:'webservice.php/faculty',
                        timeout:10000,
                        cache:false
                    });

                    request.done(function (x) {
                        if (x.result == true) {
                            $('.notification-container p').addClass('notification-success').html('The system successfully added the faculty member.');

                            var request = $.ajax({
                                async:false,
                                type:'GET',
                                dataType:'json',
                                url:'webservice.php/faculty',
                                timeout:10000,
                                cache:false
                            });

                            request.done(function (x) {
                                var faculty = '';

                                if (x.result !== null) {
                                    for (var a = 0; a < x.result.length; a++) {
                                        faculty += '<tr class="' + x.result[a].id + '">' +
                                            '<td><input type="radio" name="selectfaculty" class="table-check" value="' + x.result[a].id + '" /></td>' +
                                            '<td>' + x.result[a].name + '</td>' +
                                            '<td>' + x.result[a].netid + '</td>' +
                                            '</tr>';
                                    }
                                }
                                else {
                                    faculty = '<tr><td colspan="4">No faculty exist in database.</td></tr>';
                                }

                                $('table tbody').html(faculty);
                            });
                        }
                        else {
                            $('.notification-container p').addClass('notification-error').html('The system was unable to add the faculty member.');
                        }
                    });
                    request.fail(function () {
                        $('.notification-container p').addClass('notification-error').html('The system was unable to add the faculty member.');
                    });

                    $('.add-faculty-container #netid').val('');
                    $(this).addClass('hidden').dialog('close');
                },
                'Cancel':function () {
                    $(this).dialog('close');
                }
            }
        });

        $('.add-faculty-container').removeClass('hidden').dialog('open');
    });
});