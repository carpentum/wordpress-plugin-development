<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('carbon_fields_register_fields', 'create_options_page');
function create_options_page()
{
    Container::make('theme_options', __('Contact Form'))
        ->add_fields([
            Field::make('checkbox', 'contact_plugin_active', __('Active')),
            Field::make('text', 'contact_plugin_reciepients', __('Recipient email'))
                ->set_attribute('placeholder', 'e.g.email@email.com')
                ->set_help_text('The email that the form is submitted to'),
            Field::make('textarea', 'contact_plugin_message', __('Confirmation message'))
                ->set_attribute('placeholder', 'Enter confirmation message')
                ->set_help_text('Type the message you want the submitter to receive')

        ]);
}

add_action('after_setup_theme', 'load_carbon_fields');
function load_carbon_fields()
{
    \Carbon_Fields\Carbon_Fields::boot();
}
