<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Email
 * @author    Patrick Daly <patrick@developdaly.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Patrick Daly
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// @TODO: Define uninstall functionality here