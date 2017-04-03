<?php
header('Access-Control-Allow-Origin: *');
/*
* Commercial Codebase by WP Realty - RETS PRO Development Team.
* Copyright - WP Realty - RETS PRO - 2009 - 2016 - All Rights Reserved
* License: http://retspro.com/faq/license/
*/ini_set('display_errors', 1);error_reporting(E_ALL^E_NOTICE);
function domain($domainb)
{
$bits = explode('/', $domainb);
if ($bits[0]=='http:' || $bits[0]=='https:')
{
$domainb= $bits[2];
}
else
{
$domainb= $bits[0];
}
unset($bits);
$bits = explode('.', $domainb);
$idz=count($bits);
$idz-=3;
if (strlen($bits[($idz+2)])==2)
{
$url=$bits[$idz].'.'.$bits[($idz+1)].'.'.$bits[($idz+2)];
}
else if (strlen($bits[($idz+2)])==0)
{
$url=$bits[($idz)].'.'.$bits[($idz+1)];
}
else
{
$url=$bits[($idz+1)].'.'.$bits[($idz+2)];
}
return $url;
}
$check_access = $_SERVER['HTTP_REFERER'];
$check_access = preg_replace("#(.*?)\?.*#","$1",$check_access);
//$check_access = preg_replace("#wp-realty\d?/wp-realty\d?\.php#","",$check_access);
$exp = explode("/",$check_access,-2);
$check_access = implode("/",$exp)."/";
$check_access = domain($check_access);
session_start();
error_reporting(E_ALL^E_NOTICE);
$exp = explode("/",$_SERVER['SCRIPT_FILENAME'],-4);
$wordpress_basepath = implode("/",$exp)."/";
$blog_id = $_POST['blog_id'];
if(is_numeric($blog_id) AND $blog_id>1)
{
$wpradmin_name = "wpradmin_".$blog_id;
}else
$wpradmin_name = "wpradmin";
require_once($wordpress_basepath.$wpradmin_name."/config.php");
$wordpress_url = str_replace($wpradmin_name."/","",$config['wpradmin_baseurl']);
$wordpress_url = domain($wordpress_url);
/*var_dump($wordpress_url);
var_dump($check_access);*/
if($wordpress_url==$check_access)
{
$sql = "SELECT * FROM `".$config['table_prefix']."users` WHERE user_username='".$_POST['username']."'";
//echo $sql;
if($reU = $dbClass->GetOneRow($sql))
{
$postvars['wprealty_login'] = $reU['user_username'];
$postvars['wprealty_password'] = $reU['user_password'];
$postvars['wprealty_submit'] = 1;
}
if($loginClass->GetLoginPost($postvars,$info))
{
$_SESSION['wordpress_plugin'] = true;
}
}
?>