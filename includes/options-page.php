<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options' );
function crb_attach_theme_options() {
    Container::make( 'theme_options', __( 'Contact Form' ) )
        ->set_icon( 'dashicons-media-text' )
        ->add_fields( array(
            Field::make( 'checkbox', 'contact_plugin_active', __( 'Active' ) )
            ->set_option_value( 'yes' ),

            Field::make( 'text', 'contact_plugin_recipients', __('Recipients Email') )
            ->set_attribute( 'placeholder', 'eg. your@email.com' )
            ->set_help_text( 'The email that the form is submitted to' ),

            Field::make( 'textarea', 'contact_plugin_message', __('Confirmation message') )
            ->set_attribute( 'placeholder', 'Enter information message' )
            ->set_help_text( 'Type the message you want the submitter to receiver' ),

        ) );

}