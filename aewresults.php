<?php
/**
 * Plugin Name: Results of AEW
 * Plugin URI: https://burkie.com
 * Description: Display a custom number of recent results from All Elite Wrestling matches.
 * Author: Burkie
 * Version: 0.5
 * Author URI: https://aewresults.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: results-of-aew
 * Domain Path: /languages
 * Requires at least: 4.9
 * Requires PHP: 5.2.4
 * === RELEASE NOTES ===
 * Check readme file for full release notes.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Invalid request.' );
}

function aewresults_shortcode($atts) {

    // Set default attributes, including 'count' and 'wrestler'
    $atts = shortcode_atts(array(
        'count' => '5', 
        'wrestler' => '', // Default is empty, meaning no specific wrestler
    ), $atts);

    // Check if a wrestler is provided and adjust the feed URL accordingly
    if (!empty($atts['wrestler'])) {
        // Replace spaces with hyphens for wrestler names in the feed URL
        $wrestler_feed = 'https://aewresults.com/feeds/' . sanitize_title($atts['wrestler']);
        $rss = fetch_feed($wrestler_feed);
    } else {
        $rss = fetch_feed('https://aewresults.com/aewfeed/');
    }

    // If no errors in fetching the feed
    if ( !is_wp_error($rss)) {

        $items = $rss->get_items(0, intval($atts['count']));
        $output = '<ul class="aew">';
        foreach ($items as $item) {
            $description = $item->get_description();
            if ( ! empty($description) ) {
                $output .= '<li>' . wp_kses_post($description) . '</li>';
            }
        }
        $output .= '</ul>';
        return $output;
    } else {
        return 'Error fetching recent AEW Results.';
    }
}
add_shortcode('aewresults', 'aewresults_shortcode');

?>