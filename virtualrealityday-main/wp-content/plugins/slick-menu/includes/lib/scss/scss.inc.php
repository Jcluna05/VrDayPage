<?php
	
if (version_compare(PHP_VERSION, '5.6') < 0) {
	
    include_once __DIR__ . '/scss.legacy.inc.php';
    
}else if (! class_exists('ScssPhp\ScssPhp\Version', false)) {

    include_once __DIR__ . '/scss.latest.inc.php';
}
