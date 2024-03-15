<?php
add_shortcode('contact', 'show_contact_form');
add_action('rest_api_init', 'create_rest_endpoint');
add_action('init', 'create_submissions_page');

function create_submissions_page()
{

    $args = [
        'public' => true,
        'has_archive' => true,
        'labels' => [
            'name' => 'Submissions',
            'singular_name' => 'Submissions'
        ],
        'supports' => ['custom-fields']
    ];

    register_post_type('submission', $args);
}

function show_contact_form()
{
    ob_start();
    load_template(MY_PLUGIN_PATH . '/includes/templates/contact-form.php');
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

function create_rest_endpoint()
{
    register_rest_route('v1/contact-form', 'submit', [
        'methods' => 'POST',
        'callback' => 'handle_enquiry'
    ]);
}

function handle_enquiry($data)
{
    $params = $data->get_params();

    if (!wp_verify_nonce($params['_wpnonce'], 'wp_rest')) {
        return new WP_REST_Response('There has been an error sending the email!', 422);
    }

    unset($params['_wpnonce']);
    unset($params['_wp_http_referer']);

    // Send the email message
    $headers = [];

    $admin_email = get_bloginfo('admin_email');
    $admin_name = get_bloginfo('name');

    $headers[] = "From: {$admin_name} <{$admin_email}>";
    $headers[] = "Reply-to: {$params['name']} <{$params['email']}>";
    $headers[] = "Content-type: text/html";

    $subject = "New enquiry from {$params['name']}";
    $message = '';
    $message .= "<h1>Message has been sent from {$params['name']}</h1><br><br>";

    $postarr = [
        'post_title' => $params['name'],
        'post_type' => 'submission'
    ];

    $post_id = wp_insert_post($postarr);

    foreach ($params as $label => $value) {
        $message .= '<strong>' . ucfirst($label) . "</strong>: " . $value . '<br>';

        add_post_meta($post_id, $label, $value);
    }

    wp_mail($admin_email, $subject, $message, $headers);

    return new WP_REST_Response('The message was sent successfully!', 200);
}
