<?php

/**
 * Commit changes to the database depending on databases version.
 */
function modify_sDatabase() {
	global $wpdb;

	$table_projects = $wpdb->prefix . "projects";
	$table_releases = $wpdb->prefix . "projects_releases";
	$table_category = $wpdb->prefix . "projects_category";
	$table_volumes  = $wpdb->prefix . "projects_volumes";
	$table_settings = $wpdb->prefix . "projects_settings";

	$migration = get_option('wpmanga_db', 0);

	// Initial Migration: 001
	if ($migration <= 1) {
		include('migration/001.php');
		update_option("wpmanga_db", 1);
	}

	// Database Migration: 002
	if ($migration <= 2) {
		include('migration/002.php');
		update_option("wpmanga_db", 2);
	}

	// Database Migration: 003
	if ($migration <= 3) {
		include('migration/003.php');
		update_option("wpmanga_db", 3);
	}
	// Database Migration: 004		--added by busaway--
	if ($migration <= 4) {
		include('migration/004.php');
		update_option("wpmanga_db", 4);
	}
	// Database Migration: 005
	if ($migration <= 5) {
		include('migration/005.php');
		update_option("wpmanga_db", 5);
	}
	// Database Migration: 006
	if ($migration <= 6) {
		include('migration/006.php');
		update_option("wpmanga_db", 6);
	}
	// Database Migration: 007
	if ($migration <= 7) {
		include('migration/007.php');
		update_option("wpmanga_db", 7);
	}
}

/* EOF: includes/database_migration.php */