<?php
function worknoon_child_enqueue_styles() {
    wp_enqueue_style('golo-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('worknoon-child-style', get_stylesheet_uri(), array('golo-parent-style'));
}
add_action('wp_enqueue_scripts', 'worknoon_child_enqueue_styles');
require_once get_stylesheet_directory() . '/schema-inject.php';