<?php
/*
Plugin Name: Floating Cart Button for WooCommerce
Description: A floating cart button for WooCommerce with customizable options.
Version: 1.0
Plugin URI:  https://www.junktheme.com
Author: Junk Theme
Text Domain: floating-cart-button-for-woocommerce
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function cbw_enqueue_scripts() {
    // Enqueue WooCommerce cart fragments script
    if (class_exists('WooCommerce')) {
        wp_enqueue_script('wc-cart-fragments');
    }

    // Enqueue CSS
    wp_enqueue_style('cbw-style', plugins_url('assets/css/style.css', __FILE__));

    // Enqueue JavaScript
    wp_enqueue_script('cbw-script', plugins_url('assets/js/script.js', __FILE__), array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'cbw_enqueue_scripts');

function cbw_display_floating_cart() {
    if (!class_exists('WooCommerce')) {
        return; // Exit if WooCommerce is not active
    }

    // Don't show on Cart or Checkout pages
    if (is_cart() || is_checkout()) {
        return;
    }

    $cart_count = WC()->cart->get_cart_contents_count();
    $icon_color = get_option('cbw_icon_color', '#ffffff');
    $bg_color = get_option('cbw_bg_color', '#000000');
    $position = get_option('cbw_position', 'bottom-right');

    // Always show the floating cart button
    echo '<div id="cbw-floating-cart" class="cbw-position-' . esc_attr($position) . '" style="background-color: ' . esc_attr($bg_color) . ';">';
    echo '<a href="' . esc_url(wc_get_cart_url()) . '">';
    echo '<span class="cbw-icon" style="color: ' . esc_attr($icon_color) . ';">&#128722;</span>'; // Cart icon
    echo '<span id="cbw-cart-count" class="cbw-count">' . esc_html($cart_count) . '</span>';
    echo '</a>';
    echo '</div>';
}
add_action('wp_footer', 'cbw_display_floating_cart');

function cbw_ajax_cart_count_fragment($fragments) {
    $cart_count = WC()->cart->get_cart_contents_count();
    $fragments['#cbw-cart-count'] = '<span id="cbw-cart-count" class="cbw-count">' . esc_html($cart_count) . '</span>';
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'cbw_ajax_cart_count_fragment');

function cbw_add_options_page() {
    add_menu_page(
        'Cart Button Settings',
        'Cart Button',
        'manage_options',
        'cbw-settings',
        'cbw_render_options_page',
        'dashicons-cart',
        100
    );
}
add_action('admin_menu', 'cbw_add_options_page');

function cbw_render_options_page() {
    ?>
    <div class="wrap">
        <h1>Cart Button for WooCommerce Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('cbw_options_group');
            do_settings_sections('cbw-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function cbw_register_settings() {
    register_setting('cbw_options_group', 'cbw_icon_color', 'sanitize_hex_color');
    register_setting('cbw_options_group', 'cbw_bg_color', 'sanitize_hex_color');
    register_setting('cbw_options_group', 'cbw_position', 'sanitize_text_field');

    add_settings_section('cbw_main_section', 'Main Settings', null, 'cbw-settings');

    add_settings_field('cbw_icon_color', 'Icon Color', 'cbw_icon_color_callback', 'cbw-settings', 'cbw_main_section');
    add_settings_field('cbw_bg_color', 'Background Color', 'cbw_bg_color_callback', 'cbw-settings', 'cbw_main_section');
    add_settings_field('cbw_position', 'Button Position', 'cbw_position_callback', 'cbw-settings', 'cbw_main_section');
}
add_action('admin_init', 'cbw_register_settings');

function cbw_icon_color_callback() {
    $icon_color = get_option('cbw_icon_color', '#ffffff');
    echo '<input type="color" name="cbw_icon_color" value="' . esc_attr($icon_color) . '">';
}

function cbw_bg_color_callback() {
    $bg_color = get_option('cbw_bg_color', '#000000');
    echo '<input type="color" name="cbw_bg_color" value="' . esc_attr($bg_color) . '">';
}

function cbw_position_callback() {
    $position = get_option('cbw_position', 'bottom-right');
    echo '<select name="cbw_position">
            <option value="bottom-left" ' . selected($position, 'bottom-left', false) . '>Bottom Left</option>
            <option value="bottom-right" ' . selected($position, 'bottom-right', false) . '>Bottom Right</option>
          </select>';
}