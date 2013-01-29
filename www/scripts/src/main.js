/*!
 * main.js
 *
 * Script methods for loading faculty member data
 * Project: McCormick Faculty Advancement Tracking System
 * Author: Geoffrey Pagel
 * Licensed under the MIT license
 */
$(document).ready(function () {
    $('.toggle-selected').click(function () {
        $('.toggle-selected').not(this).removeClass('selected');
        $(this).toggleClass('selected');
    });

    $('.btn').click(function () {
        var id = $('li.selected').attr('data-value');

        $.ajax({
            url:'webservice.php/faculty/' + id,
            type:'GET',
            dataType:'json',
            contentType:'application/json',
            cache:false
        }).done(function (e) {
                if (e.result == true) {
                    window.location = '/documents.php';
                }
                else {
                    alert('Unable to load documents for the selected faculty member.');
                }
            });
    })
});