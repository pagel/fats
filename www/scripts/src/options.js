/*!
 * options.js
 *
 * Script methods for application option transactions
 * Project: McCormick Faculty Advancement Tracking System
 * Author: Geoffrey Pagel
 * Licensed under the MIT license
 */
$(document).ready(function () {
    $('a.btn-active').click(function (e) {
        var payload = $('form').serialize();

        $('input[type=checkbox]').each(function () {
            if (!this.checked) {
                payload += '&' + this.name + '=0';
            }
        });

        var request = $.ajax({
            async:false,
            type:'PUT',
            dataType:'json',
            url:'webservice.php/options',
            data:payload,
            timeout:10000,
            cache:false
        });

        request.done(function (x) {
            if (x.result) {
                $('div.notification-container p').toggleClass('notification-success').html('The system successfully saved the application options.');
            }
            else {
                $('div.notification-container p').toggleClass('notification-error').html('The system was unable to save the application options.');
                return false;
            }
        });
        request.fail(function () {
            $('div.notification-container p').toggleClass('notification-error').html('The system was unable to save the application options.');
            return false;
        });
    });
});