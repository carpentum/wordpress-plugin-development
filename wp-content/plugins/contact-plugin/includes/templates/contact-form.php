<?php if (get_plugin_options('contact_plugin_active')) : ?>

    <div id="form_success" style="background:green;color:white"></div>
    <div id="form_error" style="background:red;color:white"></div>

    <form id="enquiry_form">

        <?php wp_nonce_field('wp_rest'); ?>

        <label for="name">Name</label><br>
        <input type="text" name="name"><br><br>

        <label for="email">Email</label><br>
        <input type="text" name="email"><br><br>

        <label for="phone">Phone</label><br>
        <input type="text" name="phone"><br><br>

        <label for="message">Your message</label><br>
        <textarea name="message" id="message"></textarea><br><br>

        <button type="submit">Submit form</button>
    </form>

    <script>
        $(document).ready(function() {
            $('#enquiry_form').submit(function(event) {
                event.preventDefault();

                var form = $(this);

                $.ajax({
                    type: "POST",
                    url: "<?php echo get_rest_url(null, 'v1/contact-form/submit'); ?>",
                    data: form.serialize(),
                    success: function(res) {
                        form.hide();
                        $("#form_success").html(res).fadeIn();
                    },
                    error: function(res) {
                        $("#form_error").html(res.responseText.replace(/\"/g, "")).fadeIn();
                    }
                })
            })
        })
    </script>

<?php else : ?>

    This form is not active

<?php endif; ?>