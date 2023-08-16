<?php
/*
 * Plugin Name:       WordPress Reports
 * Plugin URI:        https://technostacks.com/
 * Description:       Easily review and export Wordpress Reports, helps when you try to migrate website and need to know the status of each sites.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Darshan patani
 * Author URI:        https://technostacks.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wordpress-report-technostacks
 */

 if ( ! defined( 'ABSPATH' ) ) {
	die();
}

 if ( ! class_exists( 'WP_Debug_Data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
}

if( !class_exists('WordpressReports') ){

    class WordpressReports {

        protected $plugin_url;
        protected $assets_url;

        public function __construct(){
            $this->plugin_url = plugins_url( 'wordpress-report-technostacks/', 'wordpress-report-technostacks' );
            $this->assets_url = $this->plugin_url.'assets/';
            add_action( 'admin_menu', [$this, 'wordpress_reports_admin_page_menu'] );
            add_action( 'admin_enqueue_scripts', [$this, 'wordpress_reports_assets'] );
        }

        public function wordpress_reports_admin_page_menu(){

            add_menu_page( 
                __( 'WP Reports Technostacks', 'wordpress-report-technostacks' ),
                'WP Reports',
                'manage_options',
                'wordpress-report-technostacks',
                [$this, 'wordpress_reports_admin_page'],
                'dashicons-media-spreadsheet',
                6
            );
        }

        public function wordpress_reports_assets(){
            if( isset($_GET['page']) && $_GET['page'] == 'wordpress-report-technostacks') {
                wp_enqueue_style( 'wp-report-style', $this->assets_url.'css/style.css', '', '' );
                wp_enqueue_script( 'jspdf', $this->assets_url.'js/jspdf.debug.js', 'jquery', TRUE );
                wp_enqueue_script( 'wp-report-app', $this->assets_url.'js/app.js', 'jspdf', TRUE );
            }
        }

        public function wordpress_reports_admin_page(){

            $plugins = get_plugins();
            $active_plugins = get_option('active_plugins');

            $themes = wp_get_themes();
            $active_theme = wp_get_theme();

            $sizes_fields = array( 'uploads_size', 'themes_size', 'plugins_size', 'wordpress_size', 'database_size', 'total_size' );
            
            $info = WP_Debug_Data::debug_data();

            $wpcore = $info['wp-core']['fields'];
            $wpserver = $info['wp-server']['fields'];
            $wpdatabase = $info['wp-database']['fields'];
            ?>
            <div class="wrap">
                <h1 class="wp-heading-inline">WordPress Reports</h1>
                <button type="button" class="button" id="export_report" data-pdfname="<?= sanitize_title( 'WordPress Reports '.date('d-m-y') ); ?>">Export</button>

                <div class="wrap-content">
                    <h2 class="wp-heading-inline">WordPress Info</h2>
                    <table class="wp-list-table widefat fixed striped table-view-list">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach($wpcore as $field_name => $field){
                            if ( is_array( $field['value'] ) ) {
                                $values = '<ul>';

                                foreach ( $field['value'] as $name => $value ) {
                                    $values .= sprintf( '<li>%s: %s</li>', esc_html( $name ), esc_html( $value ) );
                                }

                                $values .= '</ul>';
                            } else {
                                $values = esc_html( $field['value'] );
                            }

                            if ( in_array( $field_name, $sizes_fields, true ) ) {
                                printf( '<tr><td>%s</td><td class="%s">%s</td></tr>', esc_html( $field['label'] ), esc_attr( $field_name ), $values );
                            } else {
                                printf( '<tr><td>%s</td><td>%s</td></tr>', esc_html( $field['label'] ), $values );
                            }
                        }
                        ?>
                        </tbody>
                    </table>

                    <h2 class="wp-heading-inline">Plugins</h2>
                    <table class="wp-list-table widefat fixed striped table-view-list">
                        <thead>
                            <tr>
                                <th>Plugin Name</th>
                                <th>Version</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($plugins as $key => $plugin): ?>
                            <tr>
                                <td><?= $plugin['Name']; ?></td>
                                <td><?= $plugin['Version']; ?></td>
                                <td><?= (in_array($key, $active_plugins)) ? 'Active' : 'Inactive'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h2 class="wp-heading-inline">Themes</h2>
                    <table class="wp-list-table widefat fixed striped table-view-list">
                        <thead>
                            <tr>
                                <th>Theme Name</th>
                                <th>Version</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($themes as $key => $theme): ?>
                            <tr>
                                <td><?= $theme['Name']; ?></td>
                                <td><?= $theme['Version']; ?></td>
                                <td><?= ($active_theme->Name == $theme['Name']) ? 'Active' : 'Inactive'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h2 class="wp-heading-inline">Server Info</h2>
                    <table class="wp-list-table widefat fixed striped table-view-list">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach($wpserver as $field_name => $field){
                            if ( is_array( $field['value'] ) ) {
                                $values = '<ul>';

                                foreach ( $field['value'] as $name => $value ) {
                                    $values .= sprintf( '<li>%s: %s</li>', esc_html( $name ), esc_html( $value ) );
                                }

                                $values .= '</ul>';
                            } else {
                                $values = esc_html( $field['value'] );
                            }

                            if ( in_array( $field_name, $sizes_fields, true ) ) {
                                printf( '<tr><td>%s</td><td class="%s">%s</td></tr>', esc_html( $field['label'] ), esc_attr( $field_name ), $values );
                            } else {
                                printf( '<tr><td>%s</td><td>%s</td></tr>', esc_html( $field['label'] ), $values );
                            }
                        }
                        ?>
                        </tbody>
                    </table>

                    <h2 class="wp-heading-inline">Database Info</h2>
                    <table class="wp-list-table widefat fixed striped table-view-list">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach($wpdatabase as $field_name => $field){
                            if ( is_array( $field['value'] ) ) {
                                $values = '<ul>';

                                foreach ( $field['value'] as $name => $value ) {
                                    $values .= sprintf( '<li>%s: %s</li>', esc_html( $name ), esc_html( $value ) );
                                }

                                $values .= '</ul>';
                            } else {
                                $values = esc_html( $field['value'] );
                            }

                            if ( in_array( $field_name, $sizes_fields, true ) ) {
                                printf( '<tr><td>%s</td><td class="%s">%s</td></tr>', esc_html( $field['label'] ), esc_attr( $field_name ), $values );
                            } else {
                                printf( '<tr><td>%s</td><td>%s</td></tr>', esc_html( $field['label'] ), $values );
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <h6 class="float-right">Developed by Darshan Patani</h6>
            </div>
            <?php
        }

    }

    new WordpressReports();
}