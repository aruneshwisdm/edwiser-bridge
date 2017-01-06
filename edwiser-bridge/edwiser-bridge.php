<?php

namespace app\wisdmlabs\edwiserBridge;

/*
 * The plugin bootstrap file
 *
 *
 * @link    https://edwiser.org
 * @since   1.0.0
 * @package Edwiser Bridge
 *
 * @wordpress-plugin
 * Plugin Name:       Edwiser Bridge - WordPress Moodle LMS Integration
 * Plugin URI:        https://edwiser.org/bridge/
 * Description:       Edwiser Bridge integrates WordPress with the Moodle LMS. The plugin provides an easy option to import Moodle courses to WordPress and sell them using PayPal. The plugin also allows automatic registration of WordPress users on the Moodle website along with single login credentials for both the systems.
 * Version:           1.2.0
 * Author:            WisdmLabs
 * Author URI:        https://edwiser.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       eb-textdomain
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-eb-activator.php.
 */
function activateEdwiserBridge($netWide)
{
    require_once plugin_dir_path(__FILE__).'includes/class-eb-activator.php';
    EBActivator::activate($netWide);
}

register_activation_hook(__FILE__, 'app\wisdmlabs\edwiserBridge\activateEdwiserBridge');

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-eb-deactivator.php.
 */
function deactivateEdwiserBridge()
{
    require_once plugin_dir_path(__FILE__).'includes/class-eb-deactivator.php';
    EBDeactivator::deactivate();
}

register_deactivation_hook(__FILE__, 'app\wisdmlabs\edwiserBridge\deactivateEdwiserBridge');

/*
 * Applied to the list of links to display on the plugins page (beside the activate/deactivate links).
 *
 * A nes link is added that takes user to plugin settings.
 */
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'app\wisdmlabs\edwiserBridge\wdmAddSettingsActionLink');

function wdmAddSettingsActionLink($links)
{
    $pluginlinks = array(
        '<a href="'.admin_url('/admin.php?page=eb-settings').'">' . __('Settings', 'eb-textdomain') . '</a>',
            //'<a href="https://edwiser.org/bridge/documentation/" target="_blank">Documentation</a>',
    );

    return array_merge($links, $pluginlinks);
}

/*
 * Show row meta on the plugin screen, custom docs link added.
 */
add_filter('plugin_row_meta', 'app\wisdmlabs\edwiserBridge\wdmPluginRowMeta', 10, 2);

function wdmPluginRowMeta($links, $file)
{
    if ($file == plugin_basename(__FILE__)) {
        $row_meta = array(
            'docs' => '<a href="https://edwiser.org/bridge/documentation/" target="_blank"
                        title="'.esc_attr(__('Edwiser Bridge Documentation', 'eb-textdomain')).'">'.
            __('Documentation', 'eb-textdomain').
            '</a>',
        );

        return array_merge($links, $row_meta);
    }

    return (array) $links;
}

/*
 * Always show warning if legacy extensions are active
 *
 * @since 1.1
 */
add_action('admin_init', 'app\wisdmlabs\edwiserBridge\wdmShowLegacyExtensions');

function wdmShowLegacyExtensions()
{
    // prepare extensions array
    $extensions = array(
        'selective_sync' => array('selective-synchronization/selective-synchronization.php', '1.0.0'),
        'woocommerce_integration' => array('woocommerce-integration/bridge-woocommerce.php', '1.0.4'),
        'single_signon' => array('edwiser-bridge-sso/sso.php', '1.0.0'),
    );

    // legacy extensions
    foreach ($extensions as $extension) {
        if (is_plugin_active($extension[0])) {
            $plugin_data = get_plugin_data(WP_PLUGIN_DIR.'/'.$extension[0]);

            if (isset($plugin_data['Version'])) {
                if (version_compare($plugin_data['Version'], $extension[1]) <= 0) {
                    add_action('admin_notices', 'app\wisdmlabs\edwiserBridge\wdmShowLegacyExtensionsNotices');
                }
            }
        }
    }
}

function wdmShowLegacyExtensionsNotices()
{
    ob_start();
    ?>
    <div class="error">
        <p>
            <?php
            printf(
                __('Please update all %s extensions to latest version.', 'eb-textdomain'),
                '<strong>' . __('Edwiser Bridge', 'eb-textdomain') . '</strong>'
            );
            ?>
        </p>
    </div>
    <?php
    echo ob_get_clean();
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-eb.php';






$wdm_mucp_plugin_data = array(
    'plugin_short_name' => 'Edwiser Bridge',
    'plugin_slug' => 'edwiser-bridge',
    'plugin_version' => '1.2.0',
    'plugin_name' => 'Edwiser Bridge',
    'store_url' => 'http://wisdmlabs:wisdmlabs@wisdmlabs.mirealux.com/',
    'author_name' => 'WisdmLabs',
);
if (!class_exists('EBMUSelectPluginUpdater')) {
    include 'update_plugin.php';
}
set_site_transient('update_plugins', null);
// setup the updater
new EBMUSelectPluginUpdater($wdm_mucp_plugin_data['store_url'], __FILE__, array(
    'version' => $wdm_mucp_plugin_data['plugin_version'], // current version number
    'license' => "3c6b815d77c60a0aa0abd2bcbe064445", // license key (used get_option above to retrieve from DB)
    'item_name' => $wdm_mucp_plugin_data['plugin_name'], // name of this plugin
    'author' => $wdm_mucp_plugin_data['author_name'], //author of the plugin
        ));



/**
 * Defines the current plugin version
 */

add_action('admin_init', 'app\wisdmlabs\edwiserBridge\processUpgrade');
function processUpgrade()
{
    $newVersion = "1.2.0";
    $currentVersion = get_option("eb_current_version");
    if ($currentVersion == false || $currentVersion != $newVersion) {
        require_once plugin_dir_path(__FILE__) . 'includes/class-eb-activator.php';
        EBActivator::activate(false);
        update_option("eb_current_version", $newVersion);
    }
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function runEdwiserBridge()
{
    edwiserBridgeInstance()->run();
}

runEdwiserBridge(); // start plugin execution
