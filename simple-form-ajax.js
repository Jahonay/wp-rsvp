jQuery(document).ready(function ($) {
    $('#rsvp_form').on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData();
        console.log('stuff');
        $.ajax({
            type: 'POST',
            url: simpleFormAjax.ajax_url,
            data: {
                action: 'simple_form',
                nonce: simpleFormAjax.nonce,
                simple_input: formData
            },
            success: function (response) {
                if (response.success) {
                    $('#rsvp_form').remove();
                    $('#simple-form-result').html('<p>' + response.data + '</p>');
                }
                console.log(response.success);
            }
        });
    });
});