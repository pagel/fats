/*!
 * documents.js
 *
 * Script methods for document-related transactions
 * Project: McCormick Faculty Advancement Tracking System
 * Author: Geoffrey Pagel
 * Licensed under the MIT license
 */
$(document).ready(function () {
    // Handle checkbox clicks to enable/disable buttons
    $('body').on('click', 'table tbody input', function (e) {
        if ($('table tbody input:checkbox:checked').length <= 0) {
            $('#delete').removeClass('btn-active').addClass('btn-inactive');
            $('#download').removeClass('btn-active').addClass('btn-inactive');
        }
        else {
            $('.btn-inactive').removeClass('btn-inactive').addClass('btn-active');
        }
    })

    // Hide tree navigation elements that are collapsed
    $('li.navigation-item a.collapsed').parent().children('ul').hide();

    // Handle folder link clicks
    $('li.navigation-item a').click(function (e) {
        $(this).parent().attr('data-parent') != '2' ? $('#upload').removeClass('btn-inactive').addClass('btn-active') : $('#upload').removeClass('btn-active').addClass('btn-inactive');

        // Clear any error messages
        $('.table-error').addClass('hidden').empty();

        // Deactivate download and delete buttons
        $('#delete').removeClass('btn-active').addClass('btn-inactive');
        $('#download').removeClass('btn-active').addClass('btn-inactive');

        // Toggle the collapse class and display children accordingly
        $('.selected').removeClass('selected');
        $(this).toggleClass('collapsed').toggleClass('selected');

        // If the collapse class is now present, hide children. Otherwise, show them.
        $(this).hasClass('collapsed') ? $(this).parent().children('ul').hide() : $(this).parent().children('ul').show();

        var folderId = $(this).parent().attr('data-id');

        var request = $.ajax({
            async:false,
            type:'GET',
            dataType:'json',
            url:'webservice.php/documents/' + folderId,
            timeout:10000,
            cache:false
        });

        request.done(function (x) {
            var documents = '';

            if (x.result !== null) {
                for (var a = 0; a < x.result.length; a++) {
                    documents += '<tr>' +
                        '<td><input type="checkbox" id="' + x.result[a].id + '" /></td>' +
                        '<td>' + x.result[a].file_name + '</td>' +
                        '<td>' + x.result[a].mimetype + '</td>' +
                        '<td>' + x.result[a].file_size + '</td>' +
                        '</tr>';
                }
            }
            else {
                documents = '<tr><td colspan="4">No documents exist in this folder.</td></tr>';
            }

            $('table tbody').html(documents);
        });
        request.fail(function (x) {
            $('.table-error').removeClass('hidden').html('The system was unable to retrieve the document(s) in the specified folder.');
        });
    })

    // Handle delete button clicks
    $('body').on('click', '#delete.btn-active', function (e) {
        // Remove existing messages
        $('div.notification-container p').removeClass('notification-error notification-success').empty();

        var selectedIds = new Array(),
            selectedRows = new Array();

        $('tbody input:checked').each(function () {
            selectedIds.push($(this).attr('id'));
            selectedRows.push($(this).parent().parent());
        });

        var confirmationMessage = '<p>Are you sure you want to delete the selected document(s)?</p>';
        $('.confirmation-container').html(confirmationMessage);

        $('.confirmation-container').dialog({
            modal:true,
            draggable:false,
            autoOpen:false,
            title:'Confirmation Required',
            buttons:{
                'Confirm':function () {
                    $(this).dialog('close');

                    var request = $.ajax({
                        async:false,
                        type:'DELETE',
                        dataType:'json',
                        url:'webservice.php/documents/' + selectedIds.join('-'),
                        timeout:10000,
                        cache:false
                    });

                    request.done(function (x) {
                        if (x.result == true) {
                            for (var i = 0; i < selectedRows.length; i++) {
                                $(selectedRows[i]).remove();
                            }

                            $('#delete').removeClass('btn-active').addClass('btn-inactive');
                            $('#download').removeClass('btn-active').addClass('btn-inactive');

                            $('div.notification-container p').addClass('notification-success').html('The system successfully deleted the document(s).');
                        }

                        // If we got a string response, something went wrong
                        if (typeof x.result === 'string') {
                            $('div.notification-container p').addClass('notification-error').html(x.result);
                        }
                    });
                    request.fail(function (x) {
                        $('div.notification-container p').addClass('notification-error').html('The system was unable to delete the document(s).');
                    });
                },
                'Cancel':function () {
                    $(this).dialog('close');
                }
            }
        });

        $('.confirmation-container').dialog('open');
    });

    // Handle download button clicks
    $('body').on('click', '#download.btn-active', function (e) {
        // Remove existing messages
        $('div.notification-container p').removeClass('notification-error notification-success').empty();

        var selectedIds = new Array();

        $('tbody input:checked').each(function () {
            selectedIds.push($(this).attr('id'));
        });

        var request = $.ajax({
            async:false,
            type:'GET',
            dataType:'json',
            url:'webservice.php/download/' + selectedIds.join('-'),
            timeout:10000,
            cache:false
        });

        request.done(function (x) {
            if (x.result === null) {
                $('div.notification-container p').addClass('notification-error').html('The system was unable to prepare the documents for download.');
            }
            else {
                var message = '<p>The system created a zip archive of the files you requested.&nbsp;&nbsp;<a href="' + x.result + '">Click here</a> to download the archive</p>';

                $('.file-download-container').html(message);

                $('.file-download-container').dialog({
                    modal:true,
                    draggable:false,
                    autoOpen:false,
                    title:'File Ready for Download',
                    close:function () {
                        $('.file-download-container').empty();
                        $('tbody input:checked').each(function () {
                            $(this).prop('checked', false);
                        });
                        $('#download').removeClass('btn-active').addClass('btn-inactive');
                    }
                });

                $('.file-download-container').dialog('open');
            }
        });

        request.fail(function (x) {
            $('div.notification-container p').addClass('notification-error').html('The system was unable to prepare the documents for download.');
        });
    });

    $('#downloadall').click(function (e) {
        // Remove existing messages
        $('div.notification-container p').removeClass('notification-error notification-success').empty();

        var request = $.ajax({
            async:false,
            type:'GET',
            dataType:'json',
            url:'webservice.php/download',
            timeout:10000,
            cache:false
        });

        request.done(function (x) {
            if (x.result === null) {
                $('div.notification-container p').addClass('notification-error').html('The system was unable to prepare the documents for download.');
            }
            else {
                var message = '<p>The system created a zip archive of the files you requested.&nbsp;&nbsp;<a href="' + x.result + '">Click here</a> to download the archive</p>';

                $('.file-download-container').html(message);

                $('.file-download-container').dialog({
                    modal:true,
                    draggable:false,
                    autoOpen:false,
                    title:'File Ready for Download',
                    close:function () {
                        $('.file-download-container').empty();
                    }
                });

                $('.file-download-container').dialog('open');
            }
        })
    });

    // Handle file uploads
    $('body').on('click', '#upload.btn-active', function (e) {
        $('.file-upload-container').dialog({
            modal:true,
            width:525,
            autoOpen:false,
            draggable:false,
            title:'Upload File'
        });

        $('.file-upload-container').removeClass('hidden').dialog('open');
    });
});