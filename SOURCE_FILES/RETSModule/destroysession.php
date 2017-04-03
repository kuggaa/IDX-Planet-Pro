<?php
/*
* Commercial Codebase by WP Realty - RETS PRO Development Team.
* Copyright - WP Realty - RETS PRO - 2009 - 2016 - All Rights Reserved
* License: http://retspro.com/faq/license/
*/
session_start();
if($_SESSION['wordpress_plugin']==true)
{
unset($_SESSION['wprealty_password']);
unset($_SESSION['wprealty_login']);
unset($_SESSION['wordpress_plugin']);
}
?>