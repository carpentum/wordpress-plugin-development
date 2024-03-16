<?php
add_shortcode('contact', 'show_contact_form');
add_action('rest_api_init', 'create_rest_endpoint');
add_action('init', 'create_submissions_page');
add_action('add_meta_boxes', 'create_meta_box');
add_action('manage_submission_posts_columns', 'custom_submission_columns');
add_action('manage_submission_posts_custom_column', 'fill_submission_columns', 10, 2);

function fill_submission_columns($column, $post_id)
{
    switch ($column) {
        case 'name':
            echo get_post_meta($post_id, 'name', true);
            break;
        case 'email':
            echo get_post_meta($post_id, 'email', true);
            break;
        case 'phone':
            echo get_post_meta($post_id, 'phone', true);
            break;
        case 'message':
            echo get_post_meta($post_id, 'message', true);
            break;
    }
}

function custom_submission_columns($columns)
{
    $columns = [
        'cb' => $columns['cb'],
        'name' => __('Name', 'contact-plugin'),
        'email' => __('Email', 'contact-plugin'),
        'phone' => __('phone', 'contact-plugin'),
        'message' => __('message', 'contact-plugin'),
    ];
    return $columns;
}

function create_meta_box()
{
    add_meta_box('custom_contact_form', 'Submission', 'display_submission', 'submission');
}

function display_submission()
{
    $postmetas = get_post_meta(get_the_ID());

    unset($postmetas['_edit_lock']);

    // echo '<ul>';
    // foreach ($postmetas as $key => $value) {
    //     echo '<li><strong>' . ucfirst($key) . '</strong><br>' . $value[0] . '</li>';
    // }
    // echo '</ul>';


    echo '<ul>';
    echo '<li><strong>Name:</strong><br>' . get_post_meta(get_the_ID(), 'name', true) . '</li>';
    echo '<li><strong>Email:</strong><br>' . get_post_meta(get_the_ID(), 'email', true) . '</li>';
    echo '<li><strong>Phone:</strong><br>' . get_post_meta(get_the_ID(), 'phone', true) . '</li>';
    echo '<li><strong>Message:</strong><br>' . get_post_meta(get_the_ID(), 'message', true) . '</li>';
    echo '</ul>';
}

function create_submissions_page()
{

    $args = [
        'public' => true,
        'has_archive' => true,
        'labels' => [
            'name' => 'Submissions',
            'singular_name' => 'Submissions'
        ],
        'supports' => false,
        // 'supports' => ['custom-fields']
        'capability_type' => 'post',
        'capabilities' => [
            'create_posts' => false
        ],
        'map_meta_cap' => true     // Set to false if users are not allowed to edit/delete existing posts
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
        'post_type' => 'submission',
        'post_status' => 'publish',
    ];

    $post_id = wp_insert_post($postarr);

    foreach ($params as $label => $value) {
        $message .= '<strong>' . ucfirst($label) . "</strong>: " . $value . '<br>';

        add_post_meta($post_id, $label, $value);
    }

    wp_mail($admin_email, $subject, $message, $headers);

    return new WP_REST_Response('The message was sent successfully!', 200);
}
