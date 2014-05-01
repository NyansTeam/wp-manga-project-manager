<?php

/**
 * Database Migration: 005
 */

if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_releases}'") == $table_releases ) {
	$wpdb->query("ALTER TABLE `{$table_releases}`
		ADD `status`  MEDIUMINT(9) NOT NULL DEFAULT '0' AFTER `language`
	");
}

/* EOF: includes/migration/005.php */