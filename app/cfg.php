<?php
define('PATH', '/web'); //path of the website. without trailing slash, e.g.: '/web'
define('WEB_NAME', 'Petitions Online'); // website name
define('WEB_MASTER_EMAIL', 'admin@admin.com'); // your adminstration email
$db_host = "localhost:8082";
$db_user = "root";
$db_pass = "";
$db_name = "petitions_online";
$petition_total_tags = ['Human Rights', 'Politics', 'Health', 'Economics', 'Justice', 'Animals', 'Nature', 'Family', 'Entertainment', 'Immigration', 'Education', 'Other'];
$maximum_tags_can_choose = 3;
// --> DO NOT TOUCH ANYTHING BELOW <--
define('MAXIMUM_TAGS_CAN_CHOOSE', $maximum_tags_can_choose);
define('ALL_PETITION_TAGS', $petition_total_tags);
define('PHP_BASE_PATH', __DIR__);
define('ADMIN_DIR_NAME', 'administration');
define('IMAGE_TYPES', ['image/png', 'image/jpeg']);
?>
