<?php

/**
 * Database Migration: 007
 */

if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_projects}'") == $table_projects ) {
	$wpdb->query("ALTER TABLE `{$table_projects}`
		ADD `team_origin` text AFTER `image_thumbnail`
	");
}

/* EOF: includes/migration/007.php */