<?php
/*
* Commercial Codebase by WP Realty - RETS PRO Development Team.
* Copyright - WP Realty - RETS PRO - 2009 - 2016 - All Rights Reserved
* License: http://retspro.com/faq/license/
*/
global $config,$dbClass,$wpdb;
if(!isset($dbClass))
{
if(isset($wpdb->blogs))
{
$sql = "SELECT blog_id FROM `".$wpdb->blogs."` ORDER BY blog_id DESC";
$results = $wpdb->get_results($sql);
for($i=0;$i<count($results);$i++)
{
$blog_id = $results[$i]->blog_id;
if($blog_id>1)
$wpradmin_path = ABSPATH."/wpradmin_".$blog_id."/";
else
$wpradmin_path = ABSPATH."/wpradmin/";
$config_src  = $wpradmin_path."config.php";
if(file_exists($config_src))
{
require_once($config_src);
require_once($config['wpradmin_basepath']."include/install.inc.php");
$installC = new installClass($config['basepath']);
$installC->RemoveWPRPages();
$installC->DeleteDataTables();
$installC->DeleteWPR();
}
}
} else {
$config_src  = ABSPATH . "wpradmin/config.php";
if(file_exists($config_src))
{
require_once($config_src);
require_once($config['wpradmin_basepath']."include/install.inc.php");
$installC = new installClass($config['basepath']);
$installC->RemoveWPRPages();
$installC->DeleteDataTables();
$installC->DeleteWPR();
}
}
}
?>