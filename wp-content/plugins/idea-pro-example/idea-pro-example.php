<?php

/**
 * Plugin name: Idea Pro Example plugin
 * Description: This is just an example plugin
 */

function ideapro_example_function()
{
    $content = 'This is a very basic plugin';
    $content .= '<div>This is a div</div>';
    $content .= '<p>This is a block of paragraph text.</p>';

    return $content;
}

add_shortcode('example', 'ideapro_example_function');

function ideapro_admin_menu_option()
{
    add_menu_page('Header & footer scripts', 'Site scripts', 'manage_options', 'ideapro_admin_menu', 'ideapro_scripts_page', '', 200);
}

add_action('admin_menu', 'ideapro_admin_menu_option');

function ideapro_scripts_page()
{
    if (array_key_exists('submit_scripts_update', $_POST)) {
        update_option('ideapro_header_scripts', $_POST['header_scripts']);
        update_option('ideapro_footer_scripts', $_POST['footer_scripts']);
?>
        <div id="setting-error-settings-updated" class="updated_settings_error notice is-dismissable"><strong>Settings have been saved.</strong></div>
    <?php
    }
    $header_scripts = get_option('ideapro_header_scripts', 'none');
    $footer_scripts = get_option('ideapro_footer_scripts', 'none');
    ?>
    <div class="wrap">
        <h2>Update scripts</h2>
        <form method="post" action="">
            <label for=" header_scripts">Header scripts</label>
            <textarea name="header_scripts" class="large-text"><?php print $header_scripts ?></textarea>
            <label for="footer_scripts">Footer scripts</label>
            <textarea name="footer_scripts" class="large-text"><?php print $footer_scripts ?></textarea>
            <input type="submit" name="submit_scripts_update" class="button button-primary" value="UPDATE SCRIPTS">
        </form>
    </div>
<?php
}

function ideapro_display_header_scripts()
{
    $header_scripts = get_option('ideapro_header_scripts', 'none');
    print $header_scripts;
}
add_action('wp_head', 'ideapro_display_header_scripts');

function ideapro_display_footer_scripts()
{
    $footer_scripts = get_option('ideapro_footer_scripts', 'none');
    print $footer_scripts;
}
add_action('wp_footer', 'ideapro_display_footer_scripts');
