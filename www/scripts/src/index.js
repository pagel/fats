/*!
 * index.js
 *
 * Script methods for submitting the login form
 * Project: McCormick Faculty Advancement Tracking System
 * Author: Geoffrey Pagel
 * Licensed under the MIT license
 */
$(document).ready(function () {
    $('.btn').click(function () {
        $('form').submit();
    });

    // Set focus to the NetID field
    $('#netid').focus();
});