<?php
/**
 * Handles stuff during plugin uninstallation
 *
 * @package Tutormate
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_transient( 'tutorstarter_packs' );
