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
