<?php
add_shortcode('contact', 'show_contact_form');

function show_contact_form()
{
    include MY_PLUGIN_PATH . '/includes/templates/contact-form.php';
}
