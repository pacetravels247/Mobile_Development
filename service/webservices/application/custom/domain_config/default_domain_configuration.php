<?php
/**
 * This would help in creating all the necessary files and folders when we add need
 * domain to the system.
 * This would run only when the domain is created first time.
 */

/**
 * Required Files And Folders
 */
$domain_dir = '../extras/custom/'.$domain_key_name;
/**
 * Domain Internal Folder - Array
 */
$domain_internal_folders = array(
	$domain_dir,
	$domain_dir.'/images',
	$domain_dir.'/uploads'
);
if (is_dir('../extras/custom/'.$domain_key_name) == false) {
	foreach ($domain_internal_folders as $i_k => $i_v) {
		mkdir($i_v, DIR_READ_MODE, true);
		chmod($domain_dir, DIR_WRITE_MODE);
	}
}