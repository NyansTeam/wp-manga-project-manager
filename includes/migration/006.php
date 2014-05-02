<?php

/**
 * Database Migration: 006
 */

if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_projects}'") == $table_projects ) {
	$wpdb->query("ALTER TABLE `{$table_projects}`
		ADD `description_short`  text AFTER `description`
	");
	
	$wpdb->query("ALTER TABLE `{$table_projects}`
		ADD `image_thumbnail`  tinytext AFTER `image`
	");
}

/* EOF: includes/migration/006.php */