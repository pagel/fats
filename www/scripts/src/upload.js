/*!
 * upload.js
 *
 * Script methods for uploading documents to the database
 * Project: McCormick Faculty Advancement Tracking System
 * Author: Geoffrey Pagel
 * Licensed under the MIT license
 */
$(document).ready(function () {
    // Global variables
    var bar = $('.progress-bar'),
        percent = $('.progress-percent'),
        uploadcontainer = $('.file-upload-container'),
        uploadfield = $('#file-upload-form input[type=file]'),
        message = $('.notification-container p');

    $('#file-upload-form').ajaxForm({
        beforeSubmit:function () {
            if (uploadfield.val() == '') {
                alert('You must select a file to continue.');
                return false;
            }
        },
        beforeSend:function () {
            // Show the progress bar
            $('#file-upload-form input').hide();
            $('div.progress').show();
        },
        uploadProgress:function (event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            bar.width(percentVal);
            percent.html(percentVal);
        },
        complete:function (xhr) {
            var response = $.parseJSON(xhr.responseText);
            response.result == true ? $(message).html('File uploaded successfully.') : $(message).html('File upload failed.');

            // Close the dialog, share the result, and refresh the table
            $(uploadcontainer).hide().dialog('close');
            $(message).show();

            var folderId = $('.selected').parent().attr('data-id');

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

                // Reset the form
                $(bar).removeAttr('style');
                $(percent).val('0%');
                $('div.progress').hide();
                $('#file-upload-form input').show();
                $(uploadfield).val('');
            });
            request.fail(function (x) {
                $(message).removeClass('hidden').html('The system was unable to retrieve the document(s) in the specified folder.');
            });
        }
    });
});