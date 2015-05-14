// Execute JavaScript on page load
$(function() {
    // Initialize phone number text input plugin

    // Intercept form submission and submit the form with ajax
    $('#contactForm').on('submit', function(e) {
        // Prevent submit event from bubbling and automatically submitting the
        // form
        e.preventDefault();

        // Call our ajax endpoint on the server to initialize the phone call
        $.ajax({
            url: '/call',
            method: 'POST',
            data: {
                phoneNumber: $('#phoneNumber').val(),
                carpeta: $('#carpeta').val(),
                sonido: $('#sonido').val()
            }
        }).done(function(data) {
            // The JSON sent back from the server will contain a success message
            alert(data.message);
            console.log(data);
        }).fail(function(error) {
            alert(JSON.stringify(error));
        });
    });
});
