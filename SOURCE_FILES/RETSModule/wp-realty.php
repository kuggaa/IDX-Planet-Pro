<?php
/*
Plugin Name: RETS PRO - WPR3x 3.1.1
Description: <strong>RETS PRO - WPR3x</strong> This plugin was developed in order to provide real estate professionals with an easy and powerful way to publish listings within your WordPress blog. Fully compatable with WordPress 4.x - 4.+ themes, you can easily add your listings directly to your site and instantly increase the relevance of the content associated with your listings. For more information contact the developers at http://retspro.com
Author: RETS PRO TEAM
Version: 3.1.1
License: Commercial - See attached license.txt file. http://retspro.com/faq/license/
*/
//var_dump($_GET);
if (!empty($_GET)) {
$get_safe = $_GET;
}
if (!empty($_POST)) {
$post_safe = $_POST;
}
ini_set("display_errors", 0);
// mm 10-19-2015
/*if (!isset($config))
include ABSPATH . '/wpradmin/config.php';
define('MBX_SYSTEM_PATH', str_replace('core/', 'mbx', $config['wpradmin_basepath']));
if (TRUE)
include_once(MBX_SYSTEM_PATH . '/System/startup.php');*/
// mm 10-19-2015
// Required to get is_plugin_active()
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
// Only do this after the plugin is activated. Otherwise all hell breaks loose in the wp ui
if ( is_plugin_active( 'wp-realty/wp-realty.php' ) ){
if (1){
$bid = get_current_blog_id();
//echo ABSPATH . 'wpradmin/config.php';die();
if(file_exists(ABSPATH . 'wpradmin_'.$bid.'/config.php')){
include_once ABSPATH . 'wpradmin_'.$bid.'/config.php';
}
else{
include_once ABSPATH . 'wpradmin/config.php';
}
/*
if($bid == 0 || $bid == 1)
if(file_exists(ABSPATH . 'wpradmin/config.php')) include ABSPATH . 'wpradmin/config.php';
else
if(file_exists(include ABSPATH . 'wpradmin_'.$bid.'/config.php')) {
echo 'puke';die();
include ABSPATH . 'wpradmin_'.$bid.'/config.php';
}
else{
echo ABSPATH . 'wpradmin/config.php';die();
include ABSPATH . 'wpradmin/config.php';
}*/
}
//include ABSPATH . '/wpradmin_2/config.php';
define('MBX_SYSTEM_PATH', str_replace('core/', 'mbx', $config['wpradmin_basepath']));
//include_once(MBX_SYSTEM_PATH . '/System/startup.php');
$siteroot = preg_replace('#(wp-content.*)#','',$config["wpradmin_baseurl"]);
$wpradmin_name = 'wpradmin';
if(get_current_blog_id() > 1)
$wpradmin_name .= '_'.get_current_blog_id();
if (preg_match('%\\w/\\w%', $siteroot, $regs)){
$siteroot = $_SERVER['SERVER_NAME'];
define('MBX_SYS_SVCS_PATH', ABSPATH.'/wpradmin_'.get_current_blog_id().'/mbx');
//die(MBX_SYS_SVCS_PATH);
}
else{
$siteroot = preg_replace('#(wp-content.*)#','',$config["wpradmin_baseurl"]);
define('MBX_SYS_SVCS_PATH', ABSPATH.'/wpradmin/mbx');
}
define('MBX_WPR_AJAX_URL', $siteroot.'wpradmin/mbx/');
if(file_exists(MBX_SYS_SVCS_PATH.'/client_start.php')){
include_once MBX_SYS_SVCS_PATH.'/client_start.php';
}
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head');
// Only if on a listing details page
if(strpos($_SERVER['REQUEST_URI'], 'listing-details') !== FALSE){
// Get the mls from the url
preg_match('/.*listing-([a-zA-Z0-9]+)-.*/', $_SERVER['REQUEST_URI'], $matches);
// Get the listing id for this mls
$sql = 'SELECT listingsdb_id FROM '.WPR_TABLE_PFX.'listingsdb WHERE MLS = :mls';
$db = \Mbx\DataStore\MbxGetDb(WPR_USING_EXTERNAL_DB);
$res = $db->FetchSingleRow($sql, array(':mls' => $matches[1]));
$lid = $res['listingsdb_id'];
// Get the parser
include_once WPR_INCLUDE_PATH.'parse.inc.php';
$parser = new parseClass();
$link = '<link rel="canonical" href="{full_link_to_listing}" />';
// Parse the tag
define('CANON_LINK', $parser->MainParse($link, $lid));
// Inject the canonical link into the head
add_action('wp_head', 'wpr_inject_canon');
function wpr_inject_canon(){
echo str_replace('/wpradmin', '', CANON_LINK);
}
}
}
// Temporary for this
//wp_register_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.js', array('jquery') );
//wp_enqueue_script('select2');
//wp_enqueue_style( 'select2-css', "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css");
//wp_register_script('adv-srch', WPRADMIN_URL.'/'.$config["template_dir"].'/js/search_form.js', array('select2'), FALSE, TRUE );
//wp_enqueue_script('adv-srch');
function easyecho($string_message, $file, $line, $echoit = TRUE) {
$formattedStr = sprintf('<br /><strong>File: </strong>%s<br /><strong>Line: </strong>%d<br /><strong>Data: </strong>%s<br />', $file, $line, $string_message);
if ($echoit)
echo $formattedStr;
return $formattedStr;
}
function WprLoginCheckWpLoop() {
$login = new MbxAuth();
$isLoggedIn = $login->WprGetCurrentUser();
if ($isLoggedIn)
echo 1;
else
echo 4;
//die();
}
add_action('wp_ajax_wpr_login_check', 'WprLoginCheck');
add_action('wp_ajax_no_priv_wpr_login_check', 'WprLoginCheck');
function debug_rewrite() {
ini_set('error_reporting', -1);
ini_set('display_errors', 'On');
echo '<pre>';
add_action('parse_request', 'debug_404_rewrite_dump');
function debug_404_rewrite_dump(&$wp) {
global $wp_rewrite;
echo '<h2>rewrite rules</h2>';
echo var_export($wp_rewrite->wp_rewrite_rules(), true);
echo '<h2>permalink structure</h2>';
echo var_export($wp_rewrite->permalink_structure, true);
echo '<h2>page permastruct</h2>';
echo var_export($wp_rewrite->get_page_permastruct(), true);
echo '<h2>matched rule and query</h2>';
echo var_export($wp->matched_rule, true);
echo '<h2>matched query</h2>';
echo var_export($wp->matched_query, true);
echo '<h2>request</h2>';
echo var_export($wp->request, true);
global $wp_the_query;
echo '<h2>the query</h2>';
echo var_export($wp_the_query, true);
}
//add_action('template_redirect', 'debug_404_template_redirect', 99999);
function debug_404_template_redirect() {
global $wp_filter;
echo '<h2>template redirect filters</h2>';
echo var_export($wp_filter[current_filter()], true);
}
//add_filter('template_include', 'debug_404_template_dump');
function debug_404_template_dump($template) {
echo '<h2>template file selected</h2>';
echo var_export($template, true);
echo '</pre>';
exit();
}
}
//debug_rewrite();
add_action('init', 'CopyToWprAdmin');
function CopyToWprAdmin($data){
//echo dirname(__FILE__).'/core/mbx/signal.txt';
//die();
if(file_exists(dirname(__FILE__).'/core/mbx/signal.txt')){
// Copy over the important files
$src = dirname(__FILE__).'/core/mbx';
$dst = dirname(dirname(dirname(dirname(__FILE__)))).'/wpradmin/mbx';
//recurse_copy($src, $dst);
unlink(dirname(__FILE__).'/core/mbx/signal.txt');
$base_src = dirname(__FILE__).'/core';
$base_dst = dirname(dirname(dirname(dirname(__FILE__)))).'/wpradmin';
$info = file(dirname(__FILE__).'/core/info.conf');
//var_dump($info);
//die();
foreach($info as $xfer){
$xfer = trim($xfer);
if($xfer == 'template' || $xfer == 'fimages')
{
continue;
}
if(is_dir($base_src.'/'.$xfer)){
//echo $xfer.' DIR src: '.$base_src.'/'.$xfer.' dst: '.$base_dst.'/'.$xfer.'<br>';
recurse_copy ($base_src.'/'.$xfer, $base_dst.'/'.$xfer);
}
else{
//echo $xfer.' FILE src: '.$base_src.'/'.$xfer.' dst: '.$base_dst.'/'.$xfer.'<br>';
copy($base_src.'/'.$xfer, $base_dst.'/'.$xfer);
}
}
$wprrSrc = $base_src.'/modules/wprrets';
$wprrDst = $base_dst.'/modules/wprrets';
$ifile = $wprrSrc.'/info.conf';
$inf = file($ifile);
foreach($inf as $xfer){
$xfer = trim($xfer);
if(is_dir($wprrSrc.'/'.$xfer)){
//echo $xfer.' FILE src: '.$wprrSrc.'/'.$xfer.' dst: '.$wprrDst.'/'.$xfer.'<br>';
recurse_copy ($wprrSrc.'/'.$xfer, $wprrDst.'/'.$xfer);
}
else{
//echo $xfer.' FILE src: '.$wprrSrc.'/'.$xfer.' dst: '.$wprrDst.'/'.$xfer.'<br>';
copy($wprrSrc.'/'.$xfer, $wprrDst.'/'.$xfer);
}
}
$wprFmSrc = $base_src.'/modules/file_manager';
$wprFmDst = $base_dst.'/modules/file_manager';
$ifile = $wprFmSrc.'/info.conf';
$inf = file($ifile);
foreach($inf as $xfer){
$xfer = trim($xfer);
if(is_dir($wprFmSrc.'/'.$xfer)){
//echo $xfer.' FILE src: '.$wprFmSrc.'/'.$xfer.' dst: '.$wprFmDst.'/'.$xfer.'<br>';
recurse_copy ($wprFmSrc.'/'.$xfer, $wprFmDst.'/'.$xfer);
}
else{
//echo $xfer.' FILE src: '.$wprFmSrc.'/'.$xfer.' dst: '.$wprFmDst.'/'.$xfer.'<br>';
copy($wprFmSrc.'/'.$xfer, $wprFmDst.'/'.$xfer);
}
}
if(file_exists(dirname(__FILE__).'/core/mbx/upgrade.php')){
include_once(MBX_SYS_SVCS_PATH.'/System/functions.php');
include_once(dirname(__FILE__).'/core/mbx/upgrade.php');
if(is_callable('\\MbxUpgrade')){
//error_log('callable');
\MbxUpgrade();
}
unlink(dirname(__FILE__).'/core/mbx/upgrade.php');
}
}
}
function map_shortcode_js($hook){
$config = \Mbx\System\GetModuleConfig('MapSearch');
wp_register_script('gmapi', 'http://maps.googleapis.com/maps/api/js?libraries=drawing&key='.$config['gmap_key'], array('jquery') );
wp_enqueue_script('gmapi');
wp_register_script('mapscript', '/wp-content/plugins/wp-realty/js/listingbox_map.js', array('jquery'), '1', TRUE );
wp_enqueue_script('mapscript');
}
//add_action( 'admin_enqueue_scripts', 'map_shortcode_js' );
function recurse_copy($src,$dst) {
$dir = opendir($src);
@mkdir($dst);
while(false !== ( $file = readdir($dir)) ) {
if (( $file != '.' ) && ( $file != '..' )) {
if ( is_dir($src . '/' . $file) ) {
recurse_copy($src . '/' . $file,$dst . '/' . $file);
}
else {
copy($src . '/' . $file,$dst . '/' . $file);
}
}
}
closedir($dir);
}
if (!$countArray)
$countArray = FALSE;
$page_locations = '';
$page_tag_names = array(
'wp-realty listingdetails',
'wp-realty myaccount',
'wp-realty searchpage',
'wp-realty agentroster',
'wp-realty officeroster',
'wp-realty userroster',
'wp-realty login',
'wp-realty register'
);
$cookieClass = false; //this to make the cookie data available globally
//ini_set('error_reporting', E_ALL);reporting(E_ALL ^ E_NOTICE);
@session_start();
@ob_start();
global $config, $dbClass, $last_sc_num, $jqueryscript, $controlpanel_config2, $cookieClass;
define("PLUGIN_NAME", str_replace("/" . basename(__FILE__), "", plugin_basename(__FILE__)));
$last_sc_num = 0;
/************************************************************\
*
\*********************************************************** */
//add_action('init', 'wprealy_register_script');
//add_action('wp_head', 'wprealty_js');
//remove_action('wp_head', 'rel_canonical');
//remove_filter ('the_content', 'wpautop');
//add_filter('print_scripts_array','wprealty_print_scripts');
function wprAgentRoster($vars) {
global $config;
require_once($config['wpradmin_basepath'] . 'include/agent.inc.php');
$agentClass = registry::register('AgentClass');
if (isset($vars['office_code']))
$agentClass->office_code = $vars['office_code'];
return $agentClass->GetAgentRoster(false, false);
}
add_shortcode('wprAgentRoster', 'wprAgentRoster');
/* Auto Suggest Box Start Here */
add_shortcode('wpr_suggest_box', 'wpr_suggest_box');
add_action('wp_ajax_nopriv_wpr_autosuggest', 'wpr_autosuggest_ajax');
add_action('wp_ajax_wpr_autosuggest', 'wpr_autosuggest_ajax');
function wpr_suggest_box($attr) {
global $config;
$tplFile = $config["basepath"] . $config["template_dir"] . '/auto_suggest_form.php';
if (!file_exists($tplFile))
$tplFile = $config["wpradmin_basepath"] . 'template/default/auto_suggest_form.php';
wp_enqueue_script('jquery');
wp_enqueue_script('auto-suggest', $config['wpradmin_baseurl'] . 'js/auto-suggest.js');
wp_localize_script('auto-suggest', 'autosuggest', array('ajaxurl' => admin_url('admin-ajax.php')));
$tpl = file_get_contents($tplFile);
$retOutput = str_replace('{attr}', base64_encode(serialize($attr)), $tpl);
return $retOutput;
}
function wpr_autosuggest_ajax() {
global $config, $dbClass;
$retOutput = '';
$illegal_chars = array('  ', ' ', ',', '.', ';', ':', '?', '!', '*', '$', '&', '^', '@', '#', "\'", '\"', '(', ')');
if(isset($config["blog_id"]))
$base = str_replace('/wpradmin_'.$config["blog_id"], '', $config['baseurl']);
else
$base = str_replace('/wpradmin', '', $config['baseurl']);
$attr = unserialize(base64_decode($_POST['attr']));
$ret = preg_match_all("/[a-zA-Z0-9]{2,}+/", $_POST['keywords'], $keywords);
/* Get Template */
$tplFile = $config["basepath"] . $config["template_dir"] . '/auto_suggest_result.php';
if (!file_exists($tplFile))
$tplFile = $config["wpradmin_basepath"] . 'template/default/auto_suggest_result.php';
$template = registry::register('ParseClass');
$tmplContent = $template->GetTemplate($tplFile);
/* Search Agent Table & Populate result if enabled
* --------------------------------------------------
*/
//get config for remotedb
$remoteDBFlag = (bool) $config["masterdb_bool"];
$tmplAgentBlock = $template->GetTemplateBlock('agentresult', $tmplContent);
if ($attr['agent']) {
$fieldsToSearch = str_replace("|", ", ", $attr['agent_fields']);
$strSearch = implode("' AND CONCAT_WS(' '," . $fieldsToSearch . ") REGEXP '[[:<:]]", $keywords[0]);
$addParameters = " CONCAT_WS(', '," . $fieldsToSearch . ") REGEXP '[[:<:]]" . $strSearch . "' ";
$sql = "SELECT*FROM " . $config["table_const_prefix"] . "agents WHERE " . $addParameters . " LIMIT 10 ";
$rs = $dbClass->Query($sql/* , $remoteDBFlag */); // added remote db flag
$tmplBlock = $template->GetTemplateBlock('searchresults', $tmplAgentBlock['content']);
$blocklist = '';
$i = 0;
if ($rs->RecordCount() > 0) {
while (!$rs->EOF) {
$block = $tmplBlock['content'];
$alink_pattern = $base.'agent-roster/agent-'.$rs->fields['agent_id']."-{agent fieldvalue='agent_first_name'}-{agent fieldvalue='agent_last_name'}";
$block = str_replace('{agent_link}', $alink_pattern, $block);
$block = $template->MainParse($block, $rs->fields['agent_id']/* , $remoteDBFlag */); // added remote db flag
if ($i % 2 == 0)
$odd = 'odd';
else
$odd = 'even';
$i++;
$block = $template->ReplaceTag("{row_num_even_odd}", $odd, $block);
$rs->MoveNext();
$blocklist .= $block;
}
$retOutput .= $template->ReplaceTemplateBlock('searchresults', $blocklist, $tmplAgentBlock['content']);
}
else {
$tmplContent = $template->ReplaceTemplateBlock('agentresult', '', $tmplContent);
}
}
/* Remove it from template */ else {
$tmplContent = $template->ReplaceTemplateBlock('agentresult', '', $tmplContent);
}
$tmplOfficeBlock = $template->GetTemplateBlock('officeresult', $tmplContent);
if ($attr['office']) {
$fieldsToSearch = str_replace("|", ", ", $attr['office_fields']);
$strSearch = implode("' AND CONCAT_WS(' '," . $fieldsToSearch . ") REGEXP '[[:<:]]", $keywords[0]);
$addParameters = " CONCAT_WS(', '," . $fieldsToSearch . ") REGEXP '[[:<:]]" . $strSearch . "' ";
$sql = "SELECT*FROM " . $config["table_const_prefix"] . "offices WHERE " . $addParameters . " LIMIT 10 ";
//error_log($sql);
$rs = $dbClass->Query($sql/* , $remoteDBFlag */); // added remote db flag
$tmplBlock = $template->GetTemplateBlock('searchresults', $tmplOfficeBlock['content']);
$blocklist = '';
$i = 0;
if ($rs->RecordCount() > 0) {
while (!$rs->EOF) {
$block = $tmplBlock['content'];
$olink_pattern = $base.'office-roster/office-'.$rs->fields['office_id']."-{office fieldvalue='office_name'}";
$block = str_replace('{office_link}', $olink_pattern, $block);
//echo $block;die();
//$block = '';
$block = $template->MainParse($block, $rs->fields['office_id']/* , $remoteDBFlag */); // added remote db flag
if ($i % 2 == 0)
$odd = 'odd';
else
$odd = 'even';
$i++;
$block = $template->ReplaceTag("{row_num_even_odd}", $odd, $block);
$rs->MoveNext();
$blocklist .= $block;
}
$retOutput .= $template->ReplaceTemplateBlock('searchresults', $blocklist, $tmplOfficeBlock['content']);
}
else {
$tmplContent = $template->ReplaceTemplateBlock('officeresult', '', $tmplContent);
}
}
/* Remove it from template */ else {
$tmplContent = $template->ReplaceTemplateBlock('officeresult', '', $tmplContent);
}
/* Search Listing Table & Populate result
* --------------------------------------------------
*/
if(!isset($attr['listings'])){
$fieldsToSearch = str_replace("|", ", ", $attr['fields']);
$strSearch = implode("' AND CONCAT_WS(' '," . $fieldsToSearch . ") REGEXP '[[:<:]]", $keywords[0]);
$addParameters = " CONCAT_WS(', '," . $fieldsToSearch . ") REGEXP '[[:<:]]" . $strSearch . "' ";
$sql = "SELECT*FROM " . $config["table_const_prefix"] . "listingsdb WHERE " . $addParameters . " LIMIT 10 ";
$rs = $dbClass->Query($sql, $remoteDBFlag); // added remote db flag
$tmplListingBlock = $template->GetTemplateBlock('listingresult', $tmplContent);
$tmplBlock = $template->GetTemplateBlock('searchresults', $tmplListingBlock['content']);
$blocklist = '';
$i = 0;
if ($rs->RecordCount() > 0) {
while (!$rs->EOF) {
$block = $tmplBlock['content'];
// Preparse link
//$illegal_chars = array('  ', ' ', ',', '.', ';', ':', '?', '!', '*', '$', '&', '^', '@', '#', "\'", '\"', '(', ')');
$parser = new \parseClass();
$link = $base . "listing-details/";
$wpr_spacechar = $config['space_character'];
$pattern_listing = "listing" . $wpr_spacechar . "{listing field='mls'}" . $wpr_spacechar;
if ($config['listing_page_url'] != "") {
$pattern_listing .= $config['listing_page_url'];
} else {
$pattern_listing .= "{listing field='class_name'}-{listing field='address'}-{listing field='city'}-{listing field='state'}";
}
$custom_url_part = $parser->MainParse($pattern_listing, $rs->fields, $remoteDBFlag);
$custom_url_part = strtolower(str_replace($illegal_chars, "-", $custom_url_part));
$listing_link = $link . $custom_url_part;
$block = str_replace('{full_link_to_listing}', $listing_link, $block);
$block = $template->MainParse($block, $rs->fields, $remoteDBFlag); // added remote db flag
//var_dump($block);
if ($i % 2 == 0)
$odd = 'odd';
else
$odd = 'even';
$i++;
$block = $template->ReplaceTag("{row_num_even_odd}", $odd, $block);
$rs->MoveNext();
$blocklist .= $block;
}
$retOutput .= $template->ReplaceTemplateBlock('searchresults', $blocklist, $tmplListingBlock['content']);
//		$retOutput .= $template->ReplaceTemplateBlock('listingresult', $tmpOutput, $tmplContent);
}
}
if ($retOutput == '') {
$retOutput = '<p class="msg-error">Oops! No results found matching your search.</p>';
} else {
require_once($config['wpradmin_basepath'] . "include/core/core.inc.php");
$listing_page = false;
if ($lpInfo = FindPage('wp-realty listingdetails')) {
$listing_page = $lpInfo['post_name'];
}
$frontendPage = new frontendPage(false, $listing_page);
//$wp_realty_pages = GetWPRPages();
$retOutput = $frontendPage->ParseLinks($retOutput);
//$page = new Page();
//$retOutput = $page->ParseLinks($retOutput);
//	$retOutput2 = parse_links($retOutput);
}
echo $retOutput;
exit(0);
}
/* Auto Suggest Box End Here */
/* //[wprFavCounter]
function wprFavCounter( $atts ){
global $config,$controlpanel_config,$cookieClass;
$decoded = json_decode(stripslashes($_COOKIE['anon_favs']));
$cookieClass->saved_favs_array = $decoded;
//var_dump($decoded);
$replace = '<span id="wprFavCounterInner">'.count($decoded).'</span>';
//$replace = stripslashes($_COOKIE['anon_favs']);
$tpl = file_get_contents($config["wpradmin_basepath"].'template/'.$controlpanel_config["controlpanel_template_dir"].'/favorite_counter.php');
//echo $tpl;
$retval = str_replace('{fav_count}',$replace, $tpl);
return '<span id="wprFavCounter">'.$retval.'</span>';;
}
add_shortcode( 'wprFavCounter', 'wprFavCounter' );
function cookieCheck(){
if(!loading_config())
return false;
global $cookieClass,$dbClass,$config;
if(!$cookieClass && strpos($_SERVER['REQUEST_URI'],'ajax') === false && strpos($_SERVER['REQUEST_URI'],'admin') === false){
include($config["wpradmin_basepath"].'include/wprCookies.php');
if($cookieClass = new wprCookies()){
$cookieClass->dbclass = $dbClass;
$cookieClass->processAnonFavCookie();
}
}
}
add_action('init', 'cookieCheck'); */
//[wprlistingcount field="City" value="Gainesville"]
//require_once($config['wpradmin_basepath']."include/controlpanel.inc.php");
$count_cpl_obj = false;
$count_cpl_settings = FALSE;
function count_shortcode($atts) {
global $config, $dbClass, $countArray, $count_cpl_settings, $count_cpl_obj;
if ($count_cpl_settings == false) {
if ($count_cpl_obj == false)
$count_cpl_obj = new controlpanelClass();
$count_cpl_settings = $count_cpl_obj->GetControlPanelFields();
}
$atts['field'] = str_replace(" ", '', $atts['field']);
$arrFields = array();
$arrValues = array();
if (strpos($atts['field'], "(") === false) {
$arrFields = explode("|", $atts['field']);
$arrValues = explode("|", $atts['value']);
} else {
$arrFTmp = explode("|", $atts['field']);
$arrVTmp = explode("|", $atts['value']);
foreach ($arrFTmp as $keyIndex => $val) {
if (strpos($val, "(") === false) {
$arrFields[$keyIndex] = $val;
$arrValues[$keyIndex] = $arrVTmp[0];
array_shift($arrVTmp);
} else {
preg_match('/\d+/', $val, $matches);
$FieldName = preg_replace('/\(.*\d+.*\)/', '', $val);
$arrFields[$keyIndex] = $FieldName;
$arrValues[$keyIndex] = array_slice($arrVTmp, 0, $matches[0]);
$arrVTmp = array_slice($arrVTmp, $matches[0]);
}
}
}
$whereclause = array();
foreach ($arrFields as $keyIndex => $FieldName) {
$field_type_sql = "SHOW FIELDS FROM " . $config["table_const_prefix"] . "listingsdb where Field = '" . $FieldName . "' ";
// MasterDb flag added to function call
$ft_recordset = $dbClass->Query($field_type_sql, $count_cpl_settings['controlpanel_masterdb_bool']);
$db_field_type = $ft_recordset->fields['Type'];
if (strlen($db_field_type)) {
if (is_numeric($arrValues[$keyIndex])) {//numeric value
if (strstr($db_field_type, 'int') !== FALSE || strstr($db_field_type, 'deci') !== FALSE) {
$whereclause[] = " `" . $FieldName . "` = '" . $arrValues[$keyIndex] . "'";
} else {//treat as text
$whereclause[] = " `" . $FieldName . "` LIKE '" . $arrValues[$keyIndex] . "'";
}
} elseif (is_string($arrValues[$keyIndex]) && strstr($arrValues[$keyIndex], '-')) {//range search or hyphenated value
//test and handle
//echo $db_field_type;
if (strstr($db_field_type, 'char') !== FALSE) {//this is text
$whereclause[] = " `" . $FieldName . "` LIKE '" . $arrValues[$keyIndex] . "'";
} elseif (strstr($db_field_type, 'int') !== FALSE || strstr($db_field_type, 'deci') !== FALSE) {//requires numbers
$rangevalues = explode('-', $arrValues[$keyIndex]);
if (!is_numeric($rangevalues[0]))
return '<span style="color:red">Non numeric in [' . $FieldName . '] range [' . $rangevalues[0] . ']!</span>';
if (!is_numeric($rangevalues[1]))
return '<span style="color:red">Non numeric in [' . $FieldName . '] range [' . $rangevalues[1] . ']!</span>';
$whereclause[] = " `" . $FieldName . "` >= '" . $rangevalues[0] . "' AND  `" . $FieldName . "` <= '" . $rangevalues[1] . "'";
}
else {//some field not meant for a search
return '<span style="color:red">Unsupported search field type [' . $FieldName . ']!</span>';
}
//var_dump($ft_recordset->fields['Type']);
//return 'in testing';
} else {
if (strstr($db_field_type, 'int') !== FALSE || strstr($db_field_type, 'deci') !== FALSE)
return '<span style="color:red">[' . $FieldName . '] requires numeric value!</span>';
if (is_array($arrValues[$keyIndex])) {
$strSearch = implode("' OR `" . $FieldName . "` LIKE '", $arrValues[$keyIndex]);
$whereclause[] = " (`" . $FieldName . "` LIKE '" . $strSearch . "') ";
} else {
$whereclause[] = " `" . $FieldName . "` LIKE '" . $arrValues[$keyIndex] . "'";
}
}
} else {
return '<span style="color:red">Field not found [' . $FieldName . ']!</span>';
}
}
if (count($whereclause) > 0) {
$sql = "SELECT count(*) FROM " . $config["table_const_prefix"] . "listingsdb WHERE " . implode(" AND ", $whereclause);
//echo $sql. '<br><br>';
$val = $dbClass->GetOneRow($sql, $count_cpl_settings['controlpanel_masterdb_bool']);
//var_dump($val);
$numerical = array_values($val);
return $numerical[0];
} else {
return '0';
}
//return 'testval';
}
add_shortcode('wprlistingcount', 'count_shortcode');
function temp_js_insert() {
$formScript = '<script type="text/javascript">
function readySearchForm(form)
{
var str = "";
var elem = form.elements;
for(var i = 0; i < elem.length; i++)
{
//if(elem[i].name == "class_name"){
//elem[i].name = "class_name[]";
//}
if (elem[i].value==""){
elem[i].removeAttribute("name");
}
//str += "Type:" + elem[i].type + " ";
//str += "Name:" + elem[i].name + " ";
//str += "Value:" + elem[i].value + " ";
}
//alert(str);
}
</script>';
echo $formScript;
}
add_action('wp_head', 'temp_js_insert');
function showAdminAlert($message, $errormsg = false) {
$return = '<br /><br /><div style="width:50%;margin:auto"';
if ($errormsg) {
$return .= ' id="message" class="error">';
} else {
$return .= ' id="message" class="updated fade">';
}
$return .= "<p><strong>" . $message . "</strong></p></div>";
echo $return;
/* if ($errormsg) {
echo '<div id="message" class="error">';
}
else {
echo '<div id="message" class="updated fade">';
}
echo "<p><strong>$message</strong></p></div>"; */
}
function check_duplicate_pagetags() {
global $config, $dbClass, $page_tag_names;
$return_array = array();
$critical_tags = array(
'wp-realty listingdetails',
'wp-realty searchpage',
'wp-realty searchresults'
);
$duplicate_array = array();
$critical_missing_array = array();
$sql = "SELECT*FROM `" . $config['table_prefix'] . "pagetaglocations` WHERE `blogid` = " . get_current_blog_id();
$reC = $dbClass->Query($sql);
$stored_array = array();
if ($reC->RecordCount() > 0) {
while (!$reC->EOF) {
$stored_array[] = $reC->fields;
$reC->MoveNext();
}
//duplicates first
$dup_ck_array = array();
foreach ($stored_array as $value) {
$dup_ck_array[$value['pagetag_post_id']] = $value['pagetag_tag_name'];
}
$duplicates_found = array();
foreach ($page_tag_names as $tag) {
$test_val = array_keys($dup_ck_array, $tag);
if (is_array($test_val) && count($test_val) > 1) {
foreach ($test_val as $val) {
$duplicates_found[$tag][] = $val;
}
//mail('Debug@YourEmailHere.com','dupe check','found dupe '.$tag);
}
}
if (count($duplicates_found) > 0) {
$dupe_warning = 'WPR Warning! - You have duplicate page tags on different pages. This will cause navigation errors!';
$single_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
$double_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
foreach ($duplicates_found as $val) {
$dupe_warning .= '<br><br>' . $single_tab . ' {' . $dup_ck_array[$val[0]] . '} tag found on these pages';
if (count($val) > 1) {
foreach ($val as $subval) {
$dupe_warning .= '<br>' . $double_tab . 'Page ID:' . $subval . ' <a href="/wp-admin/post.php?post=' . $subval . '&action=edit">Edit</a>';
}
}
}
$return_array['duplicates'] = $dupe_warning;
} else {
$return_array['duplicates'] = false;
}
} else {
$dup_ck_array = array();
}
//now missing critical
$return_array['missing'] = false;
$missing_warning = 'WPR Warning! Required page tags are missing on your system. This will break operation.<br>';
foreach ($critical_tags as $mval) {
if (!in_array($mval, $dup_ck_array)) {
$missing_warning .= '<br>{' . $mval . '} not found. Click <a href="/wp-admin/post-new.php?post_type=page">here</a> to fix this';
$return_array['missing'] = true;
}
}
if ($return_array['missing'])
$return_array['missing'] = $missing_warning;
if (!$return_array['missing'] && !$return_array['duplicates']) {
return false;
} else {
return $return_array;
}
}
function showAdminMessages() {
// Shows as an error message. You could add a link to the right page if you wanted.
//showAdminAlert("You need to upgrade your database as soon as possible...", false);
$duplicate_alert = check_duplicate_pagetags();
if ($duplicate_alert) {
if ($duplicate_alert['duplicates'])
showAdminAlert($duplicate_alert['duplicates'], true);
if ($duplicate_alert['missing'])
showAdminAlert($duplicate_alert['missing'], true);
}
//echo 'hello';
}
add_action('admin_notices', 'showAdminMessages');
//pagetag_update
function store_pagetag_info_on_update() {
global $wpdb, $config, $dbClass;
$page_tag_names = array(
'wp-realty listingdetails',
'wp-realty myaccount',
'wp-realty searchpage',
'wp-realty agentroster',
'wp-realty officeroster',
'wp-realty userroster',
'wp-realty login',
'wp-realty register',
'wp-realty searchresults'
);
//$sql  = "SELECT*FROM `".$wpdb->posts."` WHERE `post_type` = 'page' AND `post_content` LIKE '%".$tag."%' AND post_status='publish'";
$sql = "SELECT*FROM `" . $wpdb->posts . "` WHERE `post_type` = 'page' AND (";
foreach ($page_tag_names as $tag) {
$sql .= "`post_content` LIKE '%" . $tag . "%' OR ";
}
$sql = substr($sql, 0, -4);
$sql .= ") AND post_status='publish'";
$pages_returned = $dbClass->query($sql);
$dbClass->query("DELETE FROM `" . $config['table_prefix'] . "pagetaglocations` WHERE `blogid` = " . get_current_blog_id());
if ($pages_returned->recordCount() > 0) {
//truncate
//$dbClass->query("TRUNCATE `".$config['table_prefix']."pagetaglocations`");
//Changing to a more targeted approach
//$dbClass->query("DELETE FROM `".$config['table_prefix']."pagetaglocations` WHERE `blogid` = " . get_current_blog_id());
while (!$pages_returned->EOF) {
foreach ($page_tag_names as $tag_needle) {
if (strpos($pages_returned->fields['post_content'], $tag_needle) !== false) {
$ins_sql = "INSERT INTO `" . $config['table_prefix'] . "pagetaglocations` (pagetag_tag_name, pagetag_post_name, pagetag_post_id, blogid)";
$ins_sql .= " VALUES ('" . $tag_needle . "', '" . $pages_returned->fields['post_name'] . "', '" . $pages_returned->fields['ID'] . "', '" . get_current_blog_id() . "')";
$dbClass->query($ins_sql);
// mail('Debug@YourEmailHere.com','sql 4 insert tagpages',$ins_sql);
}
//$dbClass->query("INSERT INTO `".$config['table_prefix']."pagetaglocations` (pagetag_tag_name, pagetag_post_name, pagetag_post_id) VALUES (");
}
$pages_returned->MoveNext();
}
}
//mail('Debug@YourEmailHere.com','sql 4 tagpages',$sql);
}
add_action('save_post', 'store_pagetag_info_on_update');
function results_greater_than_max() {
if (TOTAL_LISTING_COUNT > MAX_LISTING_COUNT) {
return true;
} else {
return false;
}
}
/************************************************************\
* UPDATES
\*********************************************************** */
function wprealtyUpdates() {
global $config, $dbClass;
if (!loading_config())
return false;
if ($dbClass->TableExists($config['table_prefix'] . "controlpanel") AND ! $dbClass->ColumnExists($config['table_prefix'] . "controlpanel", 'controlpanel_map_lat')) {
$sql = "ALTER TABLE `" . $config['table_prefix'] . "controlpanel` ADD `controlpanel_map_lat` VARCHAR(50) NOT NULL";
$dbClass->query($sql);
}
if ($dbClass->TableExists($config['table_prefix'] . "controlpanel") AND ! $dbClass->ColumnExists($config['table_prefix'] . "controlpanel", 'controlpanel_map_lng')) {
$sql = "ALTER TABLE `" . $config['table_prefix'] . "controlpanel` ADD `controlpanel_map_lng` VARCHAR(50) NOT NULL";
$dbClass->query($sql);
}
if ($dbClass->TableExists($config['table_prefix'] . "controlpanel") AND ! $dbClass->ColumnExists($config['table_prefix'] . "controlpanel", 'controlpanel_walkscore')) {
$sql = "ALTER TABLE `" . $config['table_prefix'] . "controlpanel` ADD `controlpanel_walkscore` INT NOT NULL";
$dbClass->query($sql);
$sql = "UPDATE `" . $config['table_prefix'] . "controlpanel` SET `controlpanel_walkscore`=1";
$dbClass->query($sql);
}
if ($dbClass->TableExists($config['table_prefix'] . "controlpanel") AND ! $dbClass->ColumnExists($config['table_prefix'] . "controlpanel", 'controlpanel_streetview')) {
$sql = "ALTER TABLE `" . $config['table_prefix'] . "controlpanel` ADD `controlpanel_streetview` INT NOT NULL";
$dbClass->query($sql);
$sql = "UPDATE `" . $config['table_prefix'] . "controlpanel` SET `controlpanel_streetview`=1";
$dbClass->query($sql);
}
/*
if($dbClass->TableExists($config['table_prefix']."agents") AND !$dbClass->ColumnExists($config['table_prefix']."agents",'user_id'))
{
$sql = "ALTER TABLE `".$config['table_prefix']."agents` CHANGE `user_id` `user_id` INT( 11 ) NULL";
$dbClass->query($sql);
}
if($dbClass->TableExists($config['table_prefix']."agents") AND !$dbClass->ColumnExists($config['table_prefix']."agents",'office_id'))
{
$sql = "ALTER TABLE `".$config['table_prefix']."agents` CHANGE `office_id` `office_id` INT( 11 ) NULL";
$dbClass->query($sql);
}
if($dbClass->TableExists($config['table_prefix']."agentfields") AND !$dbClass->ColumnExists($config['table_prefix']."agentfields",'agentfields_rank'))
{
$sql = "ALTER TABLE `".$config['table_prefix']."agentfields` CHANGE `agentfields_rank` `agentfields_rank` INT( 11 ) NULL";
$dbClass->query($sql);
echo $sql;
$sql = "CHANGE `agentfields_rank_col` `agentfields_rank_col` INT( 11 ) NULL";
$dbClass->query($sql);
echo $sql;
}
*/
}
wprealtyUpdates();
function wprealy_register_script() {
global $config;
if (!loading_config())
return false;
//wp_register_script('jquery-ui-min',$config['wpradmin_baseurl'].'js/jquery-ui.min.js',array('jquery'),'3.4.1', false);
//wp_register_script('jquery-ui-min',$config['wpradmin_baseurl'].'js/jquery-ui.min.js', false);
//wp_enqueue_script('jquery');
//wp_enqueue_script('jquery-ui-min');
//wp_enqueue_script('favCookies',$config['wpradmin_baseurl'].'js/cookies/favCookie.js');
}
function selfURL() {
$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
$protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/") . $s;
$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);
return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
}
function strleft($s1, $s2) {
return substr($s1, 0, strpos($s1, $s2));
}
/* function wprealty_js(){
global $config;
if(!loading_config())
return false;
echo "\n<!--[Begin WP Realty Code Inclusion]-->"."\n";
echo '<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>'."\n";
$added_script = array("cycle.js","jquery.alerts.js","alertinfo.js");
$added_styles = array("jquery-ui-1.8.16.custom.css","jquery.alerts.css")
//add js
for($i=0;$i<count($added_script);$i++)
echo "<script type='text/javascript' src='".$config['wpradmin_baseurl']."js/".$added_script[$i]."'></script>"."\n";
//add css       ;
for($i=0;$i<count($added_styles);$i++)
echo "<link rel='stylesheet' href='".$config['wpradmin_baseurl']."css/".$added_styles[$i]."' type='text/css' media='all'>"."\n";
echo "<link rel='stylesheet' href='".WP_PLUGIN_URL."/".PLUGIN_NAME."/css/style.css' type='text/css' media='all'>"."\n";
echo "<link rel='canonical' href='".selfURL()."' />"."\n";
echo '<!--[WPRealty End]-->'."\n";
} */
/************************************************************\
*
\*********************************************************** */
function GetBlogSettings($blog_id) {
global $dbClass, $config;
//die($config);
$sql = "SELECT*FROM `" . $config['table_const_prefix'] . "controlpanel` WHERE blog_id='" . $blog_id . "'";
//$sql = "SELECT*FROM `".$config['table_const_prefix']."controlpanel`";
//var_dump($config);
//die($sql);
$settings = $dbClass->query($sql);
if ($settings->recordCount() > 0) {
return $settings->fields;
} else
return false;
}
add_action('admin_menu', 'wpr_plugin_menu');
/************************************************************\
*
\*********************************************************** */
function wpr_plugin_menu() {
$baseurl = get_site_url() . "/";
$wpradmin_baseurl = $baseurl . "wpradmin/";
global $current_user;
get_currentuserinfo();
$hook = add_menu_page('RETS PRO', "RETS PRO", 8, __FILE__, 'wpr_admin_pages');
$pluginurl = WP_PLUGIN_URL . "/" . PLUGIN_NAME . "/";
$baseurl = get_site_url() . "/";
$blog_id = 0;
if (function_exists('get_current_blog_id')) {
$blog_id = get_current_blog_id();
}
//check mu options
global $dbClass, $config;
if (!loading_config(false))
return false;
$normal = true;
if (is_numeric($blog_id) AND $blog_id > 1) {
if ($settings = GetBlogSettings($blog_id)) {
//pre install
if ($settings['controlpanel_mu_option'] == 0) {
$normal = false;
}
} else
return false;
$wpradmin_name = "wpradmin_" . $blog_id;
} else
$wpradmin_name = "wpradmin";
if ($normal === true) {
$wpradmin_baseurl = $baseurl . $wpradmin_name . "/";
wp_register_script('wp-realty', $pluginurl . '/js/wpradmin_box.php?wpradmin_url=' . $wpradmin_baseurl . "&plugin_url=" . $pluginurl . "&plugin_class=" . $hook . "&username=" . $current_user->user_login . "&blog_id=" . $blog_id, array('jquery'/* ,'thickbox' */));
wp_enqueue_script('wp-realty');
add_action('admin_print_styles', 'wpr_cb_style');
add_action('admin_print_footer_scripts', 'wpr_cb_script');
}
}
add_filter("wpmu_delete_blog_upload_dir", "wpr_blog_delete");
/************************************************************\
*
\*********************************************************** */
function wpr_blog_delete($arg) {
if (preg_match("/\/(\d+)\//", $arg, $match)) {
$blog_id = $match[1];
$config_src = ABSPATH . "wpradmin_" . $blog_id . "/config.php";
if (file_exists($config_src)) {
global $config, $dbClass;
require_once($config_src);
require_once($config['wpradmin_basepath'] . "include/install.inc.php");
$installC = new installClass($config['wpradmin_basepath']);
$installC->DeleteDataTables();
$installC->DeleteWPR();
}
}
}
/************************************************************\
*
\*********************************************************** */
function wpr_admin_pages() {
//check mu options
global $dbClass, $config;
if (!loading_config(true))
return false;
$blog_id = 0;
if (function_exists('get_current_blog_id')) {
$blog_id = get_current_blog_id();
}
if (is_numeric($blog_id) AND $blog_id > 1) {
if (!$settings = GetBlogSettings($blog_id)) {
UpdateBlogSettings($blog_id);
}
if ($settings = GetBlogSettings($blog_id)) {
if (isset($_POST['save_sumbit']) AND $_POST['mu_option'] == 1) {
$url = $_SERVER['SERVER_NAME'];
$page = $_SERVER['php_SELF'];
$reload_url = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
;
InstallInstance($blog_id);
UpdateBlogSettings($blog_id, 1, true);
header('Location: ' . $reload_url);
die();
}
if ($settings['controlpanel_mu_option'] == 0) {
$echo = "
<h3>RETS PRO Settings</h3>
<a href='" . $config['baseurl'] . "'>configured with main domain click here to open</a><br/><br/>
<form method='post'>
MU Mode:
<select name='mu_option'>
<option value='0'>Share Main Domain</option>
<option value='1'>Setup New Install</option>
</select>
<input type='submit' value='Save' name='save_sumbit'>
</form>";
$echo .= "<div class='updated fade' style='margin-top: 20px; padding: 20px; width:600px'><strong>Setup Examples</strong>
<br/><br/>
If you are planning on using this subdomain as a site that you might want to use an Agent-Site or
Rentals-Site etc. then you will most likely want to use the (Setup New Install) option
<br/><br/>
But if you plan on using the subdomain as a (Community-Site) or any other type of site that would not need<br/>
its own separate data base and admin panel then you will want to use the (Share Main Domain) option
</div>";
echo $echo;
return;
}
}
}
echo "<div class='updated fade' style='margin-top: 20px; padding: 20px; width:600px'>WP Realty Successfully installed! - Please click on menu link to proceed</div>";
}
/************************************************************\
*
\*********************************************************** */
function wpr_cb_script() {
$pluginurl = WP_PLUGIN_URL . "/" . PLUGIN_NAME . "/";
echo '<script type="text/javascript" src="' . $pluginurl . 'js/jquery.colorbox-min.js"></script>';
}
/************************************************************\
*
\*********************************************************** */
function wpr_cb_style() {
$pluginurl = WP_PLUGIN_URL . "/" . PLUGIN_NAME . "/";
echo '<link href="' . $pluginurl . 'css/colorbox.css" rel="stylesheet" type="text/css" />';
}
/************************************************************\
*
\*********************************************************** */
function loading_config($check_blog = true) {
global $config, $dbClass, $wpdb;
if (!isset($dbClass) OR $dbClass->connectionID == NULL) {
$blog_id = 0;
if (isset($wpdb->blogs)) {
if (function_exists('get_current_blog_id')) {
$blog_id = get_current_blog_id();
}
}
if ($blog_id > 1 AND $check_blog !== false) {
$config_src = ABSPATH . "/wpradmin_" . $blog_id . "/config.php";
//die($config_src."2");
if (!file_exists($config_src)) {
$config_src = ABSPATH . "wpradmin/config.php";
//die($config_src."2");
require_once($config_src);
if ($settings = GetBlogSettings($blog_id)) {
//var_dump($settings);
//die($settings['controlpanel_mu_option']);
if ($settings['controlpanel_mu_option'] == 1) {
return false;
}
}
}
} else
$config_src = ABSPATH . "wpradmin/config.php";
if (file_exists($config_src)) {
require_once($config_src);
return true;
}
} else
return true;
return false;
}
add_filter('rewrite_rules_array', 'wp_realty_rewrite_rules');
/************************************************************\
*
\*********************************************************** */
function wp_realty_rewrite_rules($rules) {
global $dbClass, $config;
//die('page found');
if (!loading_config())
return false;
//$nrules = array();
foreach ($rules as $key => $value) {
if (strpos($key, "wp-realty") !== false) {
unset($rules[$key]);
}
}
/* code added by intizar */
$sqlgettype = "SELECT controlpanel_space_character from " . $config['table_prefix'] . "controlpanel where blog_id=0";
$datachar = $dbClass->Query($sqlgettype);
if ($datachar->RecordCount() > 0) {
$spacechar = $datachar->fields['controlpanel_space_character'];
}
$spacechar = isset($spacechar) ? $spacechar : "-";
/* end intizar */
//die('page found');
if ($lInfo = FindPage('wp-realty listingdetails')) {
$listing_page = $lInfo['post_name'];
$page_id = $lInfo['ID'];
}
if ($lInfo = FindPage('wp-realty agentroster')) {
$agent_roster = $lInfo['post_name'];
$agent_roster_id = $lInfo['ID'];
}
if ($lInfo = FindPage('wp-realty officeroster')) {
$office_roster = $lInfo['post_name'];
$office_roster_id = $lInfo['ID'];
}
if ($lInfo = FindPage('wp-realty userroster')) {
$user_page = $lInfo['post_name'];
$user_page_id = $lInfo['ID'];
}
if ($lInfo = FindPage('wp-realty myaccount')) {
$my_account_page = $lInfo['post_name'];
$my_account_page_id = $lInfo['ID'];
}
//echo $spacechar.'<br>';
$newrules = array();
$newrules[$listing_page . '/listing' . $spacechar . '([a-z]+-[0-9]*)' . $spacechar . '(.*)?$'] = '/?page_id=' . $page_id . '&page=listingdetails&listing_id=$matches[1]';
$newrules[$listing_page . '/listing' . $spacechar . '([0-9]*)' . $spacechar . '(.*)?$'] = '/?page_id=' . $page_id . '&page=listingdetails&listing_id=$matches[1]';
$newrules[$listing_page . '/listing' . $spacechar . '([a-z0-9]*)' . $spacechar . '(.*)?$'] = '/?page_id=' . $page_id . '&page=listingdetails&listing_id=$matches[1]';
$newrules[$listing_page . '/listing' . $spacechar . '([0-9]*)' . $spacechar . '(.*)?/delfavorite$'] = '/?page_id=' . $page_id . '&page=listingdetails&action=delfavorite&listing_id=$matches[1]';
$newrules[$listing_page . '/listing' . $spacechar . '([0-9]*)' . $spacechar . '(.*)?/addfavorite$'] = '/?page_id=' . $page_id . '&page=listingdetails&action=addfavorite&listing_id=$matches[1]';
$newrules[$office_roster . '/office' . $spacechar . '([0-9]*)' . $spacechar . '(.*)?$'] = '/?page_id=' . $office_roster_id . '&page=officeroster&office_id=$matches[1]';
$newrules[$agent_roster . '/agent' . $spacechar . '([0-9]*)' . $spacechar . '(.*)?$'] = '/?page_id=' . $agent_roster_id . '&page=agentroster&agent_id=$matches[1]';
$newrules[$agent_roster . '/letter' . $spacechar . '([A-Z]*)?$'] = '/?page_id=' . $agent_roster_id . '&page=agentroster&letter=$matches[1]';
$newrules[$office_roster . '/letter' . $spacechar . '([A-Z]*)?$'] = '/?page_id=' . $office_roster_id . '&page=officeroster&letter=$matches[1]';
$newrules[$user_page . '/user' . $spacechar . '([0-9]*)' . $spacechar . '(.*)?$'] = '/?page_id=' . $user_page_id . '&page=userroster&user_id=$matches[1]';
$newrules[$my_account_page . '/listing' . $spacechar . '([0-9]*)' . $spacechar . '(.*)?/delfavorite$'] = '/?page_id=' . $my_account_page_id . '&page=myaccount&action=delfavorite&listing_id=$matches[1]';
$newrules[$my_account_page . '/listing' . $spacechar . '([0-9]*)' . $spacechar . '(.*)?/addfavorite$'] = '/?page_id=' . $my_account_page_id . '&page=myaccount&action=addfavorite&listing_id=$matches[1]';
$newrules[$listing_page . '/listing' . $spacechar . '([a-z0-9]*)' . $spacechar . '(.*)?$'] = '/?page_id=' . $page_id . '&page=listingdetails&listing_id=$matches[1]';
$newrules[$my_account_page . '/logout'] = '/?page_id=' . $my_account_page_id . '&page=myaccount&action=logout';
$newrules[$my_account_page . '/login'] = '/?page_id=' . $my_account_page_id . '&page=myaccount&action=login';
return $newrules + $rules;
}
/************************************************************\
*
\*********************************************************** */
function ob_starter() {
ob_start('ob_receiver');
}
/************************************************************\
*
\*********************************************************** */
function SearchField($field) {
global $dbClass, $config;
$sql = "SELECT listingfields_field_name FROM `" . $config['table_prefix'] . "listingsfields` WHERE listingfields_field_caption='" . $field . "'";
$info = $dbClass->GetOneRow($sql);
if ($info === false) {
if (!$dbClass->ColumnExists($config['table_prefix'] . "listingsdb", $field)) {
if ($dbClass->ColumnExists($config['table_prefix'] . "listingsdb", "listingsdb_" . $field)) {
return "listingsdb_" . $field;
} else
return false;
} else
// hack to do province
if ($field == 'state')
return 'Province';
return $field;
} else
return $info['listingfields_field_name'];
}
/************************************************************\
*
\*********************************************************** */
function ob_parser($header) {
//echo $_GET['listing_id'];
//die();
global $config, $dbClass, $wp;
//echo var_export( $wp->request, true );
//die();
if (!loading_config())
return false;
get_seo_patterns();
//die('parser');
//require_once($config['wpradmin_basepath']."include/controlpanel.inc.php");
//$controlClass = registry::register('controlpanelClass');
$controlClass = new controlpanelClass();
$settings = $controlClass->GetControlPanelFields();
//
$wprpages = GetWPRPages(true);
global $post;
$actualPageId = $post->ID; //var_dump($_GET);
//echo $_GET['listing_id'];
//die();
//die($get_safe['listing_id']);
if ($_GET['listing_id']) {
if ($city_field_name = SearchField('city')) {
$city_field_name = ",l." . $city_field_name;
} else
$city_field_name = "";
if ($state_field_name = SearchField('state')) {
$state_field_name = ",l." . $state_field_name;
} else
$state_field_name = "";
$urlTitleColumn = "";
if ($dbClass->ColumnExists($config['table_prefix'] . "listingsdb", 'listingsdb_url_title'))
$urlTitleColumn = ',l.listingsdb_url_title';
$sql = "SELECT c.class_name, l.listingsdb_id, l.listingsdb_title $urlTitleColumn $city_field_name $state_field_name
FROM `" . $config['table_prefix'] . "listingsdb` l
LEFT JOIN `" . $config['table_prefix'] . "class` c
ON c.class_id = l.class_id
WHERE l.MLS LIKE '" . $_GET['listing_id'] . "' LIMIT 1"; //echo 'sql '.$sql;die();// MasterDb flag added to function call
try {
if ($listingInfo = $dbClass->GetOneRow($sql, $settings['controlpanel_masterdb_bool'])) {
//die('yelp');
$parse = false;
if ($urlTitleColumn != "" && $listingInfo['listingsdb_url_title'] != "") {
$parse = true;
$newTitle = $listingInfo['listingsdb_url_title'];
} elseif ($settings['controlpanel_listing_page_title'] != "") {
$parse = true;
$newTitle = $settings['controlpanel_listing_page_title'];
} else
$newTitle = $listingInfo['class_name'] . ', ' . $listingInfo['listingsdb_title'] . ', ' . $listingInfo[$city_field_name] . ', ' . $listingInfo[$state_field_name];
if ($settings['controlpanel_META_keywords_listing'] != "") {
$parse = true;
$newTags = $settings['controlpanel_META_keywords_listing'];
} else
$newTags = $listingInfo['class_name'] . ', ' . $listingInfo['listingsdb_title'] . ', ' . $listingInfo[$city_field_name] . ', ' . $listingInfo[$state_field_name];
//die('yelp2')  ;
if ($settings['controlpanel_META_Description'] != "") {
$parse = true;
$newDescription = $settings['controlpanel_META_Description'];
} else
$newDescription = $listingInfo['class_name'] . ', ' . $listingInfo['listingsdb_title'] . ', ' . $listingInfo[$city_field_name] . ', ' . $listingInfo[$state_field_name];
}
else {
//error_log("SEO title query:\n" . $sql);
}
} catch (Exception $ex) {
error_log('DB Exception: ' . $ex->getMessage());
}
} elseif (in_array($actualPageId, $wprpages)) {
$blogid = 0;
$sql = "SELECT controlpanel_default_page_title, controlpanel_default_meta, controlpanel_keyword_default_META FROM `" . $config['table_prefix'] . "controlpanel` WHERE blog_id='" . $blogid . "' LIMIT 1";
/*
$title = page_display::get_page_title($_GET['PageID']);
$description = page_display::get_page_description($_GET['PageID']);
$keywords = page_display::get_page_keywords($_GET['PageID']);
if ($title == '') {
$title = $config['seo_default_title'];
}
if ($description == '') {
$description = $config['seo_default_description'];
}
if ($keywords == '') {
$keywords = $config['seo_default_keywords'];
}
$newTags = $keywords;
$newDescription = $description;
$newTitle = $title;
*/
if ($defaults = $dbClass->GetOneRow($sql)) {
$newTitle = $defaults['controlpanel_default_page_title'];
$newTags = $defaults['controlpanel_keyword_default_META'];
$newDescription = $defaults['controlpanel_default_meta'];
}
}
$header = parse_title($header, $newTitle); //mail('mike@magicboxsoftware.com', 'new title', $newTitle. ' lid '.json_encode($_GET));
$header = parse_meta_tags($header, $newTags);
$header = parse_meta_description($header, $newDescription);
$formScript = '<script type="text/javascript">
function readySearchForm(form)
{
var str = "";
var elem = form.elements;
for(var i = 0; i < elem.length; i++)
{
//if(elem[i].name == "class_name"){
//elem[i].name = "class_name[]";
//}
if (elem[i].value==""){
elem[i].removeAttribute("name");
}
//str += "Type:" + elem[i].type + " ";
//str += "Name:" + elem[i].name + " ";
//str += "Value:" + elem[i].value + " ";
}
//alert(str);
}
</script>';
$formScript .= '<script type="text/javascript">jQuery("#map_show_hide").click(function () {
jQuery("#map").slideToggle("slow");
});</script>';
if ($parse !== false) {
//die('oops');
require_once($config['wpradmin_basepath'] . "include/parse.inc.php");
$parseClass = registry::register('parseClass');
$parseClass = new parseClass();
$header = $parseClass->MainParse($header, $listingInfo['listingsdb_id'], true);
}
//$header .= $formScript;
//die('oops2');
return $header;
}
/************************************************************\
* META TAGS
\*********************************************************** */
function parse_meta_description($header, $description = '') {
if ($description)
$header = $header . "<meta name='description' content='" . $description . "'>\n\r";
return $header;
}
/************************************************************\
*
\*********************************************************** */
function parse_meta_tags($header, $tags = '') {
if ($tags)
$header = $header . "<meta name='keywords' content='" . $tags . "'>\n\r";
return $header;
}
/************************************************************\
*
\*********************************************************** */
function parse_title($header, $title = '') {
if ($title != "") {
$reg_title = "%<title>(.*?)</title>%s";
$header = preg_replace('%<title>(.*?)</title>%s', "<title>" . $title . "</title>", $header);
}
return $header;
}
/************************************************************\
*
\*********************************************************** */
function ob_receiver($header) {
return ob_parser($header);
}
/************************************************************\
*
\*********************************************************** */
function custom_head() {
ob_end_flush();
}
add_action('template_redirect', 'ob_starter', 1);
/************************************************************\
*
\*********************************************************** */
function GetWPRPage() {
return;
global $config, $wpdb;
$page_id = $wpdb->get_results("SELECT `ID` FROM `" . $wpdb->posts . "` WHERE `post_type` = 'page' AND `post_content` LIKE '%{wp-realty%' ORDER BY `ID` LIMIT 1;", ARRAY_A);
if (!isset($page_id[0]['ID']) || (int) $page_id[0]['ID'] < 1) {
return false;
}
$page_id = (int) $page_id[0]['ID'];
$page_permalink = get_permalink($page_id);
return $page_permalink;
}
/************************************************************\
*
\*********************************************************** */
function GetWPRPages($id = false) {
global $config, $wpdb;
$sql = "SELECT*FROM `" . $wpdb->posts . "` WHERE `post_type` = 'page' AND `post_content` LIKE '%{wp-realty%' ORDER BY `ID`";
$pages = $wpdb->get_results($sql, ARRAY_A);
$page_permalinks = array();
if (!isset($pages[0]['ID']) || (int) $pages[0]['ID'] < 1) {
return false;
} else {
$reg = "#\{wp-realty\s(.*?)\}#";
for ($i = 0; $i < count($pages); $i++) {
if (preg_match($reg, $pages[$i]['post_content'], $matches)) {
$key = trim($matches[1]);
if ($id === false)
$page_permalinks[$key] = get_permalink($pages[$i]['ID']);
else
$page_permalinks[$key] = $pages[$i]['ID'];
}
}
}
return $page_permalinks;
}
/************************************************************\
*
\*********************************************************** */
function parse_links($content) {
global $config;
$page_permalink = GetWPRPage();
$reg = "#" . $config['baseurl'] . "((?:[^\w]+)|index.php(?:.*?))#is";
$content = preg_replace($reg, $page_permalink . "$1", $content);
return $content;
}
/************************************************************\
*
\*********************************************************** */
function get_seo_patterns() {
global $wpdb, $post, $config, $dbClass;
//die('found');
/* $page_id = $wpdb->get_results("SELECT `ID` FROM `".$wpdb->posts."` WHERE `post_type` = 'page' AND (`post_content` LIKE '%{wp-realty index}%' OR `post_content` LIKE '%{wp-realty}%') LIMIT 1;",ARRAY_A);
if( !isset($page_id[0]['ID']) || (int)$page_id[0]['ID'] < 1) {
$page_id = $wpdb->get_results("SELECT `ID` FROM `".$wpdb->posts."` WHERE `post_type` = 'page' AND `post_content` LIKE '%{wp-realty%' ORDER BY `ID` LIMIT 1;",ARRAY_A);
}
$page_id = (int)$page_id[0]['ID'];
$page_permalink = get_permalink($page_id);
$page_path =  str_replace('http://','', $page_permalink);
$page_path =  str_replace($_SERVER['SERVER_NAME'],'', $page_path);
$page_path =  str_replace('www.','', $page_path);
if ( substr($page_permalink, strlen($page_permalink)-1, 1) == '/')
$page_permalink = substr($page_permalink, 0, strlen($page_permalink)-1);
$permalink_structure = get_option('permalink_structure');
$request_uri = $_SERVER['REQUEST_URI'];
if (!isset($_POST['referer']) || strlen($_POST['referer']) < 6) {
if ($permalink_structure == '')
$_POST['referer'] = $page_permalink;
else
$_POST['referer'] = $page_permalink;
}
if ($permalink_structure == '') {
$path = $page_path.'&';
} else {
if (substr($page_path, strlen($page_path)-1, 1) != '/')
$path = $page_path.'/';
else
$path = $page_path;
} */
$request_uri = $_SERVER['REQUEST_URI'];
//require_once($config['wpradmin_basepath']."include/controlpanel.inc.php");
//$controlClass = registry::register('controlpanelClass');
$controlClass = new controlpanelClass();
$settings = $controlClass->GetControlPanelFields();
//die();
$spacechar = $settings['controlpanel_space_character'];
$seo_patterns = array();
if ($lInfo = FindPage('wp-realty listingdetails')) {
//die('here');
$listing_page = $lInfo['post_name'];
$listing_path = get_permalink($lInfo['ID']);
$seo_patterns[] = array(1, '#/' . $listing_page . '/listing' . $spacechar . '([A-Za-z0-9]*)' . $spacechar . '(.*?)#i', 'page=listingdetails&listing_id=%s', $listing_path . 'listing' . $spacechar . '\\1' . $spacechar . '\\2');
$seo_patterns[] = array(1, '#/' . $listing_page . '/listing' . $spacechar . '([0-9]*)' . $spacechar . '(.*?)/addfavorite#i', 'page=listingdetails&listing_id=%s&action=addfavorite', $listing_path . 'listing' . $spacechar . '\\1' . $spacechar . '\\2/addfavorite');
$seo_patterns[] = array(1, '#/' . $listing_page . '/listing' . $spacechar . '([0-9]*)' . $spacechar . '(.*?)/delfavorite#i', 'page=listingdetails&listing_id=%s&action=delfavorite', $listing_path . 'listing' . $spacechar . '\\1' . $spacechar . '\\2/delfavorite');
///$seo_patterns[] = array(1, '#/'.$listing_page.'/listing'.$spacechar.'([0-9]*)'.$spacechar.'(.*?)/printer_friendly#i', 'page=listingdetails&listing_id=%s&action=printer_friendly', $listing_path.'listing'.$spacechar.'\\1'.$spacechar.'\\2/printer_friendly');
$seo_patterns[] = array(1, '#/listing' . $spacechar . '([0-9]*)' . $spacechar . '(.*?)#i', 'page=listingdetails&listing_id=%s', $listing_path . 'listing' . $spacechar . '\\1' . $spacechar . '\\2');
//$xx = json_encode($seo_patterns);
//mail('Debug@YourEmailHere.com', 'seo patterns test', $xx);
}
if ($lInfo = FindPage('wp-realty agentroster')) {
$agent_roster = $lInfo['post_name'];
$agent_path = get_permalink($lInfo['ID']);
$seo_patterns[] = array(1, '#/' . $agent_roster . '/agent' . $spacechar . '([0-9]*)' . $spacechar . '(.*?)#i', 'agent_id=%s', $agent_path . 'agent' . $spacechar . '\\1' . $spacechar . '\\2');
$seo_patterns[] = array(1, '#/' . $agent_roster . '/letter' . $spacechar . '([A-Z]*)#i', 'letter=%s', $agent_path . 'letter' . $spacechar . '\\1');
$seo_patterns[] = array(0, '#/' . $agent_roster . '#i', '', $agent_path);
}
if ($lInfo = FindPage('wp-realty officeroster')) {
$office_roster = $lInfo['post_name'];
$office_path = get_permalink($lInfo['ID']);
$seo_patterns[] = array(1, '#/' . $office_roster . '/office' . $spacechar . '([0-9]*)' . $spacechar . '(.*?)#i', 'office_id=%s', $office_path . 'office' . $spacechar . '\\1' . $spacechar . '\\2');
$seo_patterns[] = array(1, '#/' . $office_roster . '/letter' . $spacechar . '([A-Z]*)#i', 'letter=%s', $office_path . 'letter' . $spacechar . '\\1');
$seo_patterns[] = array(0, '#/' . $office_roster . '#i', '', $office_path);
}
if ($lInfo = FindPage('wp-realty userroster')) {
$user_page = $lInfo['post_name'];
$user_path = get_permalink($lInfo['ID']);
$seo_patterns[] = array(1, '#/' . $user_page . '/user' . $spacechar . '([0-9]*)' . $spacechar . '(.*?)#i', 'user_id=%s', $user_path . 'user' . $spacechar . '\\1' . $spacechar . '\\2');
$seo_patterns[] = array(0, '#/' . $user_page . '#i', '', $user_path);
}
if ($lInfo = FindPage('wp-realty register')) {
$register_page = $lInfo['post_name'];
$register_path = get_permalink($lInfo['ID']);
$seo_patterns[] = array(0, '#/' . $register_path . '#i', 'page=register', $register_path);
}
if ($lInfo = FindPage('wp-realty login')) {
$login_page = $lInfo['post_name'];
$login_path = get_permalink($lInfo['ID']);
$seo_patterns[] = array(0, '#/' . $login_path . '#i', 'page=login', $login_path);
}
if ($lInfo = FindPage('wp-realty myaccount')) {
$myaccount_page = $lInfo['post_name'];
$myaccount_path = get_permalink($lInfo['ID']);
$seo_pattern[] = array(1, '#/' . $myaccount_page . '/listing' . $spacechar . '([0-9]*)' . $spacechar . '(.*?)/addfavorite#i', 'page=listingdetails&listing_id=%s&action=addfavorite', $myaccount_path . 'listing' . $spacechar . '\\1' . $spacechar . '\\2/addfavorite');
$seo_patterns[] = array(1, '#/' . $myaccount_page . '/listing' . $spacechar . '([0-9]*)' . $spacechar . '(.*?)/delfavorite#i', 'page=listingdetails&listing_id=%s&action=delfavorite', $myaccount_path . 'listing' . $spacechar . '\\1' . $spacechar . '\\2/delfavorite');
$seo_patterns[] = array(0, '#/' . $myaccount_page . '/logout#i', 'page=myaccount&action=logout', $myaccount_path . "logout");
/*
if($login_page==NULL)
$seo_patterns[] = array(0, '#/'.$myaccount_page.'/login#i', 'page=myaccount&action=login', $myaccount_path."login");
if($register_page==NULL)
$seo_patterns[] = array(0, '#/'.$myaccount_page.'/login#i', 'page=myaccount&action=register', $myaccount_path."register");
*/
$seo_patterns[] = array(0, '#/' . $myaccount_page . '#i', 'page=myaccount', $myaccount_path);
}
if (isset($_GET['page']))
$action = $_GET['page'];
else {
$action = '';
foreach ($seo_patterns as $seo_pattern) {
preg_match($seo_pattern[1], $request_uri, $matches);
if (count($matches) < 1) {
continue;
}
if (isset($matches[$seo_pattern[0]])) {
if ($seo_pattern[0] > 0) {
$uri = sprintf($seo_pattern[2], $matches[$seo_pattern[0]]);
//mail('Debug@YourEmailHere.com', 'seo uri', $uri);
} else
$uri = $seo_pattern[2];
//mail('Debug@YourEmailHere.com', 'seo uri2', $uri);
$uri_array = explode('&', $uri);
if (count($uri_array) > 0) {
foreach ($uri_array as $item) {
$req = explode('=', $item);
if (count($req) == 2) {
$_GET[$req[0]] = $req[1];
//mail('Debug@YourEmailHere.com', 'rewritten from seo', 'req0 = '.$req[0].' req1 = '.$req[1]);
}
}
}
}
}
}
}
/************************************************************\
*
\*********************************************************** */
function full_copy($source, $target) {
if (is_dir($source)) {
@mkdir($target);
$d = dir($source);
while (FALSE !== ( $entry = $d->read() )) {
if ($entry == '.' || $entry == '..') {
continue;
}
$Entry = $source . '/' . $entry;
if (is_dir($Entry)) {
full_copy($Entry, $target . '/' . $entry);
continue;
}
copy($Entry, $target . '/' . $entry);
}
$d->close();
} else {
copy($source, $target);
}
}
/************************************************************\
*
\*********************************************************** */
function find($dir, $pattern) {
$files = array();
if (is_dir($dir)) {
$dHandle = opendir($dir);
while (false !== ($filename = readdir($dHandle))) {
if ($filename != ".." AND $filename != ".") {
if (is_dir($dir . "/" . $filename)) {
$arr = find($dir . "/" . $filename, $pattern);
$files = array_merge($files, $arr);
} elseif ($filename == $pattern)
$files[] = $dir . "/" . $filename;
}
}
}
return $files;
}
/************************************************************\
*
\*********************************************************** */
function find_old($dir, $pattern) {
// escape any character in a string that might be used to trick
// a shell command into executing arbitrary commands
$dir = escapeshellcmd($dir);
// get a list of all matching files in the current directory
$files = glob("$dir/$pattern");
// find a list of all directories in the current directory
// directories beginning with a dot are also included
if (is_array(glob("$dir/{.[^.]*,*}", GLOB_BRACE | GLOB_ONLYDIR))) {
foreach (glob("$dir/{.[^.]*,*}", GLOB_BRACE | GLOB_ONLYDIR) as $sub_dir) {
if ($arr = find($sub_dir, $pattern)) {
$files = @array_merge($files, $arr); // merge array with files from subdirectory
}
}
}
// return all found files
return $files;
}
/************************************************************\
*
\*********************************************************** */
function mkdirFromPath($path) {
$path_parts = pathinfo($path);
$pathParts = explode("/", $path_parts['dirname']);
$spath = "";
for ($i = 0; $i < count($pathParts); $i++) {
if ($pathParts[$i] != "")
$spath .= "/" . $pathParts[$i];
if ($spath != "") {
if (!@file_exists($spath))
@mkdir($spath);
}
}
}
/************************************************************\
*
\*********************************************************** */
function InstallInstance($blog_id = 0) {
global $current_user, $wpdb;
// Required, used to insert meta data
global $config;
get_currentuserinfo();
add_filter('rewrite_rules_array', 'wp_realty_rewrite_rules');
if ($blog_id == 0)
$bId = 1;
if (function_exists('get_blog_details')) {
$blogD = get_blog_details($bId);
$blogUrl = $blogD->siteurl;
} else {
$blogUrl = get_bloginfo('wpurl');
}
if ($blog_id > 1) {//Means this is a sub
$wpradmin_path = ABSPATH . "/wpradmin_" . $blog_id . "/";
$wpradmin_url = $blogUrl . "/wpradmin_" . $blog_id . "/";
$blog_details = get_blog_details(1);
$mwpr_path = ABSPATH . "/wpradmin/";
$mwpr_url = $blog_details->siteurl . "/wpradmin/";
} else {
$wpradmin_path = ABSPATH . "/wpradmin/";
$wpradmin_url = $blogUrl . "/wpradmin/";
$mwpr_path = $wpradmin_path;
$mwpr_url = $wpradmin_url;
}
$wpradmin_path = str_replace('//', '/', $wpradmin_path);
$wpradmin_url = str_replace('//', '/', $wpradmin_url);
$wpradmin_url = str_replace('http:/', 'http://', $wpradmin_url);
$mwpr_path = str_replace('//', '/', $mwpr_path);
$mwpr_url = str_replace('//', '/', $mwpr_url);
$mwpr_url = str_replace('http:/', 'http://', $mwpr_url);
$wpradmin_include = $wpradmin_path . "include/";
$config_src = $wpradmin_path . "config.php";
$src = ABSPATH . "/wp-content/plugins/" . PLUGIN_NAME . "/include/";
$basepath = ABSPATH . "/wp-content/plugins/" . PLUGIN_NAME . "/";
$basepath = str_replace('//', '/', $basepath);
$baseurl = $blogUrl . "/wp-content/plugins/" . PLUGIN_NAME . "/";
$config_ok = false;
if (file_exists($config_src)) {
$config_ok = true;
} else {
if (!file_exists($wpradmin_path)) {
@mkdir($wpradmin_path);
if (!file_exists($wpradmin_path)) {
$message = "You can not activate the plugin because it does not have permission to create the directory.
<br/><br/>
Create a directory manually (" . $wpradmin_path . ") and activate the plugin again";
$title = "Permision danied problem";
wp_die($message, $title);
return false;
}
}
//creating a list of copied files and copy
//$cBasepath = substr($basepath,0,-1);
$cBasepath = $basepath . "core";
$infoTab = find($cBasepath, 'info.conf');
if (count($infoTab) > 0) {
for ($i = 0; $i < count($infoTab); $i++) {
$files = file($infoTab[$i]);
$bath = str_replace('info.conf', '', $infoTab[$i]);
for ($y = 0; $y < count($files); $y++) {
$source = trim($bath . $files[$y]);
if (file_exists($source)) {
$desc = str_replace($cBasepath . "/", $wpradmin_path, $source);
mkdirFromPath($desc);
full_copy($source, $desc);
}
}
}
$config_ok = true;
}
}
if ($config_ok) {
require_once($basepath . "core/include/wpr_install.inc.php");
$install_c = new wpr_installClass($wpradmin_include, $wpradmin_path, $wpradmin_url);
$request['db_username'] = DB_USER;
$request['db_password'] = DB_PASSWORD;
$request['db_host'] = DB_HOST;
$request['db_name'] = DB_NAME;
$request['admin_password'] = $current_user->user_pass;
$request['admin_username'] = $current_user->user_login;
$request['admin_email'] = $current_user->user_email;
//Here is where the problem begins
$install_c->CreateConfigFile($request, true, $basepath . "core/", $baseurl . "core/", $blog_id);
$install_c->CreateDatabaseStructure($request, true, $blog_id);
if (FindPage('wp-realty foopage', 'ID') === false)
//foo
/*        ***********************************************************\
* For entering default header and footer code per page tag
\*********************************************************** */
wpr_insert_critical_pages();
return true;
}
return false;
}
function wpr_insert_critical_pages() {
global $config;
/*    ***********************************************************\
* For entering default header and footer code per page tag
\*********************************************************** */
if (FindPage('wp-realty searchpage', 'ID') === false) {
$post = array(
'post_content' => '{wp-realty searchpage}',
'post_status' => 'publish',
'post_title' => 'Search Page',
'post_name' => 'search-page',
'post_type' => 'page'
);
$searchpage_id = wp_insert_post($post);
add_post_meta($searchpage_id, 'wpr_head', '<script></script>', TRUE);
add_post_meta($searchpage_id, 'wpr_foot', '<script></script>', TRUE);
}
if (FindPage('wp-realty listingdetails', 'ID') === false) {
$post = array(
'post_content' => '{wp-realty listingdetails}',
'post_status' => 'publish',
'post_title' => 'Listing Details',
'post_name' => 'listing-details',
'post_type' => 'page'
);
$details_page_id = wp_insert_post($post);
add_post_meta($details_page_id, 'wpr_head', "<link rel='stylesheet' href='" . $config['baseurl'] . $config['template_dir'] . '/style.css' . "' type='text/css' media='all'>
<script src='" . $config['wpradmin_baseurl'] . 'js/jquery-ui.min.js' . "' type='text/javascript'></script>
<script src='" . $config['wpradmin_baseurl'] . 'js/cycle.js' . "' type='text/javascript'></script>
<script src='" . $config['wpradmin_baseurl'] . 'js/jquery.alerts.js' . "' type='text/javascript'></script>
<script src='" . $config['wpradmin_baseurl'] . 'js/alertinfo.js' . "' type='text/javascript'></script>
<link rel='stylesheet' href='" . $config['wpradmin_baseurl'] . 'css/jquery-ui-1.8.16.custom.css' . "' type='text/css' media='all'>
<link rel='stylesheet' href='" . $config['wpradmin_baseurl'] . 'css/jquery.alerts.css' . "' type='text/css' media='all'>", TRUE);
add_post_meta($details_page_id, 'wpr_foot', '<script></script>', TRUE);
}
if (FindPage('wp-realty searchresults', 'ID') === false) {
$post = array(
'post_content' => '{wp-realty searchresults}',
'post_status' => 'publish',
'post_title' => 'Search Results',
'post_name' => 'search-results',
'post_type' => 'page'
);
$results_page_id = wp_insert_post($post);
add_post_meta($results_page_id, 'wpr_head', '<script></script>', TRUE);
add_post_meta($results_page_id, 'wpr_foot', '<script></script>', TRUE);
}
store_pagetag_info_on_update();
}
//add_action('activate_wp-realty','test');
register_activation_hook(__FILE__, 'wp_realty_install');
//if(isset($_GET['activate']) && ($_GET['activate'] == 'true')) {
//add_action('init', 'wp_realty_install');
//}
///---------------------------------------------------------------------
add_action('wp_loaded', 'wpr_flush_rules');
// flush_rules() if our rules are not yet included
function wpr_flush_rules() {
global $wp_rewrite;
//$wp_rewrite->flush_rules();
}
///----------------------------------------------------------------------
/************************************************************\
*
\*********************************************************** */
function UpdateBlogSettings($blog_id, $mu_option = 0, $replace = false) {
global $dbClass, $config;
if (!loading_config(false))
return false;
$sql = "SELECT*FROM `" . $config['table_const_prefix'] . "controlpanel` WHERE blog_id='" . $blog_id . "'";
$settings = $dbClass->query($sql);
if ($settings->recordCount() == 0) {
$sql = "INSERT INTO `" . $config['table_const_prefix'] . "controlpanel` SET controlpanel_mu_option='" . $mu_option . "',blog_id='" . $blog_id . "'";
return $dbClass->Query($sql);
} elseif ($replace !== false) {
$sql = "UPDATE `" . $config['table_const_prefix'] . "controlpanel` SET controlpanel_mu_option='" . $mu_option . "' WHERE blog_id='" . $blog_id . "'";
return $dbClass->Query($sql);
}
}
/************************************************************\
*
\*********************************************************** */
function wp_realty_install() {
global $wpdb;
if ($_GET['networkwide'] == '1') {
//$sql = "SELECT*FROM wp_blogs WHERE "
global $current_user;
//die("user ".$current_user->ID);
get_currentuserinfo();
$userBlogs = get_blogs_of_user($current_user->ID);
if (count($userBlogs) > 0) {
foreach ($userBlogs as $k => $v) {
$blog_id = $v->userblog_id;
if ($blog_id > 1) {
if (!$settings = GetBlogSettings($blog_id)) {
UpdateBlogSettings($blog_id);
AddUser();
} else {
if ($settings['controlpanel_mu_option'] == 1) {
InstallInstance($blog_id);
}
}
} else
InstallInstance();
}
}
}
else {
$blog_id = 0;
if (isset($wpdb->blogs)) {
if (function_exists('get_current_blog_id')) {
$blog_id = get_current_blog_id();
}
}
if ($blog_id > 1) {
UpdateBlogSettings($blog_id);
AddUser();
wpr_insert_critical_pages();
} else {
InstallInstance();
}
}
return true;
}
/************************************************************\
*
\*********************************************************** */
function AddUser() {
global $config;
require_once($config['wpradmin_basepath'] . "include/install.inc.php");
$install = new installClass();
global $current_user;
get_currentuserinfo();
$request['admin_password'] = $current_user->user_pass;
$request['admin_username'] = $current_user->user_login;
$request['admin_email'] = $current_user->user_email;
$install->InsertUsersData($request, true);
}
add_action('wp_head', 'custom_head', 11);
add_filter('the_content', 'wpr_check_content', 20);
add_filter('widget_content', 'wpr_check_content_widget');
add_action('wp_head', 'widget_redirect_callback');
/************************************************************\
*
\*********************************************************** */
function widget_redirect_callback() {
global $wp_registered_widgets;
foreach ($wp_registered_widgets as $id => $widget) {
array_push($wp_registered_widgets[$id]['params'], $id);
$wp_registered_widgets[$id]['callback_redirect'] = $wp_registered_widgets[$id]['callback'];
$wp_registered_widgets[$id]['callback'] = 'widget_redirected_callback';
}
}
/************************************************************\
*
\*********************************************************** */
function widget_redirected_callback() {
global $wp_registered_widgets;
$params = func_get_args();
$id = array_pop($params);
$callback = $wp_registered_widgets[$id]['callback_redirect'];
ob_start();
call_user_func_array($callback, $params);
$widget_content = ob_get_contents();
ob_end_clean();
echo apply_filters('widget_content', $widget_content, $id);
}
/************************************************************\
*
\*********************************************************** */
function get_page_locations() {
global $config, $dbClass, $page_locations;
//die('pp');
$sql = "SELECT*FROM `" . $config['table_prefix'] . "pagetaglocations` WHERE `blogid` = " . get_current_blog_id();
$reC = $dbClass->Query($sql);
if ($reC->RecordCount() > 0) {
while (!$reC->EOF) {
$page_locations[$reC->fields['pagetag_tag_name']] = array('post_name' => $reC->fields['pagetag_post_name'], 'ID' => $reC->fields['pagetag_post_id']);
$reC->MoveNext();
}
//mail('Debug@YourEmailHere.com','tags',json_encode($page_locations));
return $page_locations;
}
return false;
}
/************************************************************\
*
\*********************************************************** */
//values for $return_value ("array","ID","post_name")
function FindPage($tag, $return_value = 'array') {
global $page_locations;
if (!is_array($page_locations))
$page_locations = get_page_locations();
if (!is_array($page_locations)) {
store_pagetag_info_on_update();
$page_locations = get_page_locations();
}
if (is_array($page_locations)) {
//mail('Debug@YourEmailHere.com','tags2',json_encode($page_locations));
//die('wow');
//var_dump($page_locations);
foreach ($page_locations as $key => $val) {
//mail('Debug@YourEmailHere.com','tag arr pos',$key);
}
if (!isset($page_locations[$tag])) {
//mail('Debug@YourEmailHere.com','tag find fail','tag= '.$tag);
return false;
}
if ($return_value == 'array') {
//mail('Debug@YourEmailHere.com','tag find array','tag= '.$tag.' loc '.$page_locations[$tag]);
return $page_locations[$tag];
}
//mail('Debug@YourEmailHere.com','tag find single','tag= '.$tag.' loc '.$page_locations[$tag][$return_value]);
return $page_locations[$tag][$return_value];
}
return false;
}
/************************************************************\
*
\*********************************************************** */
function GetSearchID($type = 0) {
global $config, $dbClass;
$sql = "SELECT id FROM `" . $config['table_prefix'] . "searchengines` WHERE searchengine_type='" . $type . "' LIMIT 1";
$reC = $dbClass->Query($sql);
if ($reC->RecordCount() > 0) {
return $reC->fields['id'];
}
return false;
}
/************************************************************\
*
\*********************************************************** */
function wpr_check_content_widget($content) {
return wpr_check_content($content, true);
}
/************************************************************\
*
\*********************************************************** */
function wpr_check_content($content, $widget = false) {
//die('1234');
$content = str_replace("\xe2\x80\x93", '-', $content); // utf8
$content = str_replace(chr(150), '-', $content); // 1252
//convert single and double quotes html entities into characters in $content for wp-realty parsing - BPS 11/21/14
$char_codes = array('&#8216;', '&#8217;', '&#8220;', '&#8221;', '&#8242;', '&#8243;');
$replacements = array("'", "'", '"', '"', "'", '"');
$content = str_replace($char_codes, $replacements, $content);
global $config, $dbClass, $UrlClass, $wpdb;
$blog_id = 0;
if (isset($wpdb->blogs)) {
if (function_exists('get_current_blog_id')) {
$blog_id = get_current_blog_id();
}
if ($blog_id <= 1)
$blog_id = 0;
}
if (!loading_config())
return false;
get_seo_patterns();
//die('found seo');
$baseurl = get_site_url() . "/";
$config['baseurl'] = $baseurl;
if (isset($_GET['listing_id']) AND $widget != true) {
// MasterDb flag added to function call, dbClass is checking is connection available or not, else connect normal db
$sql = "SELECT listingsdb_id FROM " . $config['table_prefix'] . "listingsdb WHERE MLS='" . $_GET['listing_id'] . "' LIMIT 1";
$reslistingID = $dbClass->Query($sql, true);
if ($reslistingID->RecordCount() > 0) {
$_GET['listing_id'] = $reslistingID->fields['listingsdb_id'];
} else {
$_GET['listing_id'] = 1;
}
}
$_GET['foobar'] = 'foo';
require_once($config['wpradmin_basepath'] . "include/core/core.inc.php");
$listing_page = false;
if ($lpInfo = FindPage('wp-realty listingdetails')) {
$listing_page = $lpInfo['post_name'];
}
$frontendPage = new frontendPage(false, $listing_page);
//die('fount');
//tags wp-realty
if (preg_match_all('/{wp-realty (\w*?)(\s+[^}]*?)?}/', $content, $tags_found)) {
$reg_search = "#search=(.*?)$#is";
//$reg_param = "#(\w+)\s*=(?:'([^']*)'|\"([^\"]*)\")#";
$reg_param = "#(\w+)\s*=([^\s]*)#";
for ($i = 0; $i < count($tags_found[0]); $i++) {
$tag = $tags_found[1][$i];
if (!empty($tags_found[2][$i])) {
$param = trim($tags_found[2][$i]);
if (preg_match_all($reg_param, $param, $matches_params)) {
for ($i_p = 0; $i_p < count($matches_params[0]); $i_p++) {
if ($matches_params[2][$i_p] != "")
$params[$matches_params[1][$i_p]] = $matches_params[2][$i_p];
else
$params[$matches_params[1][$i_p]] = $matches_params[3][$i_p];
}
}
}
if ($widget === true) {
unset($_GET);
}
if ($_GET['page'] != 'searchresults') {
if ($tag == 'listingdetails') {
$_GET['page'] = 'listingdetails';
} elseif ($tag == 'officeroster') {
$_GET['page'] = 'officeroster';
} elseif ($tag == 'agentroster') {
$_GET['page'] = 'agentroster';
} elseif ($tag == 'userroster') {
$_GET['page'] = 'userroster';
} elseif ($tag == 'myaccount') {
$_GET['page'] = 'myaccount';
} elseif ($tag == 'register') {
$_GET['page'] = 'register';
} elseif ($tag == 'login') {
$_GET['page'] = 'login';
} elseif ($tag == 'advsearch') {
if ($_GET['page'] !== 'searchresults') {
if ($search_id = GetSearchID(2)) {
$_GET['page'] = 'searchpage';
$_GET['search_id'] = $search_id;
}
}
} elseif ($tag == 'search' AND $widget == true) {
//if($search_id  = GetSearchID(1))
//{
$_GET['page'] = 'searchpage';
$_GET['search_id'] = $params['id'];
//unset($_GET['listing_id']);
//$_GET['search_id'] = $search_id;
//}
}
}
//$Ftemplate = $config['basepath'].$config['template_dir']."/wpr_frontend_template.html";
$frontendPage->frontendTemplate = "{content}";
if (!isset($_GET['page'])) {
if ($_GET['page'] !== 'searchresults') {
$_GET['page'] = 'searchpage';
if (is_numeric($params['id'])) {
$_GET['search_id'] = $params['id'];
}
/*
elseif($search_id  = GetSearchID(1))
{
$_GET['page'] = 'searchpage';
$_GET['search_id'] = $search_id;
}
*/
}
}
if ($_GET['page'] == "searchresults" AND $widget === false) {
$pageInfo2 = FindPage('wp-realty searchresults', 'array');
//mail('Debug@YourEmailHere.com', 'page name', $_GET['page'].' pi2 '.$pageInfo2['post_name']);
if ($pageInfo = FindPage('wp-realty searchresults', 'array')) {
$gets = $_GET;
if ($config['baseurl'] . $pageInfo['post_name'] != $UrlClass->BaseUrl()) {
if ($url_query = parse_url($UrlClass->selfURL(), PHP_URL_QUERY)) {
$redirect_url = $config['baseurl'] . $pageInfo['post_name'] . "/?" . ($url_query);
//die();
header("Location: " . $redirect_url);
die();
}
}
}
} elseif ($widget === true) {
$_GET['page'] == $tag;
}
$wp_realty_pages = GetWPRPages();
$frontendPage->ParseTemplate("", false, $wp_realty_pages);
$frontent_content = $frontendPage->ReturnPage();
$frontent_content = $frontendPage->ParseLinksWP($frontent_content, $wp_realty_pages);
$content = str_replace($tags_found[0][$i], $frontent_content, $content);
$content = parse_links($content);
}
}
//tags shortcode
if (preg_match_all('/\[(shortcode[^\[\]]*?)\]/', $content, $tags_found)) {
for ($i = 0; $i < count($tags_found[0]); $i++) {
//   $parseClass = new parseClass();
$shortcode = "{" . $tags_found[1][$i] . "}";
require_once($config['wpradmin_basepath'] . "include/core/core.inc.php");
//$frontendPage = new frontendPage();
$frontendPage->frontendTemplate = $shortcode;
unset($_GET);
$wp_realty_pages = GetWPRPages();
$frontendPage->ParseTemplate("", false);
$frontent_content = $frontendPage->ReturnPage();
$frontent_content = $frontendPage->ParseLinksWP($frontent_content, $wp_realty_pages);
$content = str_replace($tags_found[0][$i], $frontent_content, $content);
$content = parse_links($content);
}
}
//form
if (preg_match_all('/\[form_(\d+)\]/', $content, $tags_found)) {
for ($i = 0; $i < count($tags_found[0]); $i++) {
//   $parseClass = new parseClass();
$formcode = "{wprcontactform id='" . $tags_found[1][$i] . "'}";
require_once($config['wpradmin_basepath'] . "include/core/core.inc.php");
//$frontendPage = new frontendPage();
$frontendPage->frontendTemplate = $formcode;
unset($_GET['page']);
$wp_realty_pages = GetWPRPages();
$frontendPage->ParseTemplate("", false);
$frontent_content = $frontendPage->ReturnPage();
$frontent_content = $frontendPage->ParseLinksWP($frontent_content, $wp_realty_pages);
$content = str_replace($tags_found[0][$i], $frontent_content, $content);
$content = parse_links($content);
}
}
//convert single and double quotes characters back into html entities for $content - BPS 11/21/14
$content = wptexturize($content);
return $content;
}
add_action('media_buttons', 'listing_option_box', 100);
add_action('admin_head', 'listing_option_box_add_js');
/************************************************************\
*
\*********************************************************** */
function listing_option_box() {
if (!loading_config())
return false;
$baseurl = get_bloginfo('url') . "/wp-content/plugins/" . PLUGIN_NAME . "/core/";
global $post_ID, $temp_ID;
global $config;
global $dbClass;
$context = "%s";
wp_enqueue_style('thickbox');
$blog_id = 0;
if (function_exists('get_current_blog_id')) {
$blog_id = get_current_blog_id();
}
$wpradmin_name = "wpradmin";
// See if we are in a sub
if (is_numeric($blog_id) AND $blog_id > 1) {
$wpradmin_name = "wpradmin_" . $blog_id; // Rename the config path
$config_src = ABSPATH . $wpradmin_name . "/config.php";
if (!file_exists($config_src)) {
$wpradmin_name = "wpradmin";
}
} else
$config_src = ABSPATH . $wpradmin_name . "/config.php";
$config_src = str_replace("/", "__", base64_encode($config_src));
$out = '
<style>
#media-buttons{padding-top: 4px;}
</style>
<a href="' . $baseurl . 'include/listingbox.php?action=select_listingbox&config=' . $config_src . '&TB_iframe=true&width=840&height=753"
id="listingbox" class="thickbox button" title="Listing Box" onclick="return false;">
<img src=' . $baseurl . 'images/shortcode.png />Embed Listings</a>';
printf($context, $out);
}
/************************************************************\
*
\*********************************************************** */
function listing_option_box_add_js() {
$baseurl = get_bloginfo('url') . "/wp-content/plugins/" . PLUGIN_NAME . "/";
echo "<script src='" . $baseurl . "js/listingbox_js.js' type='text/javascript'></script>";
}
/*******************************************************************************************\
* Code for inserting "Custom Header/Footer" options located below wordpress wysiwyg editor *
\****************************************************************************************** */
add_action('plugins_loaded', 'wpr_loaded');
function wpr_loaded() {
global $wpr_fields;
$wpr_fields = array('wpr_head', 'wpr_foot');
define('wpr_plugin_folder', str_replace('\\', '/', dirname(__FILE__)));
define('wpr_plugin_path', '/' . substr(wpr_plugin_folder, stripos(wpr_plugin_folder, 'wp-content')));
define('wpr_metaboxname', ' WPR Custom Head & Footer Code Options');
add_action('admin_init', 'wpr_init');
add_action('wp_head', 'wpr_head_inject');
add_action('wp_footer', 'wpr_foot_inject');
}
function wpr_head_inject() {
global $post;
if (isset($post->ID)) {
$v = get_post_meta($post->ID, 'wpr_head', TRUE);
if ($v)
echo "\n" . $v . "\n";
}
}
function wpr_foot_inject() {
global $post;
if (isset($post->ID)) {
$v = get_post_meta($post->ID, 'wpr_foot', TRUE);
if ($v)
echo "\n" . $v . "\n";
}
}
function wpr_init() {
add_meta_box('wpr_metaoptions', __(wpr_metaboxname, 'wpr'), 'wpr_metaoptions', 'post', 'normal', 'high');
add_meta_box('wpr_metaoptions', __(wpr_metaboxname, 'wpr'), 'wpr_metaoptions', 'page', 'normal', 'high');
add_action('save_post', 'wpr_save_meta');
}
function wpr_metaoptions() {
global $config, $post, $wpr_fields;
foreach ($wpr_fields as $field_name) {
${$field_name} = get_post_meta($post->ID, $field_name, TRUE);
}
include($config['basepath'] . $config['template_dir'] . '/wprmeta.php');
echo '<input type="hidden" name="wpr_options_noncename" id="wpr_options_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />' . "\n";
}
function wpr_save_meta($post_id) {
global $wpr_fields;
// make sure all new data came from the proper wpr entry fields
if (!wp_verify_nonce($_POST['wpr_options_noncename'], plugin_basename(__FILE__))) {
return $post_id;
}
if ($_POST['post_type'] == 'page') {
if (!current_user_can('edit_page', $post_id))
return $post_id;
}
else {
if (!current_user_can('edit_post', $post_id))
return $post_id;
}
// save data
foreach ($wpr_fields as $field_name) {
$current_data = get_post_meta($post_id, $field_name, TRUE);
$new_data = $_POST[$field_name];
if ($current_data) {
if ($new_data == '')
delete_post_meta($post_id, $field_name);
elseif ($new_data != $current_data)
update_post_meta($post_id, $field_name, $new_data);
}
elseif ($new_data != '') {
add_post_meta($post_id, $field_name, $new_data, TRUE);
}
}
}
/*******************************************************************************************\
*  WP Realty Widget*with tinymce wysiwyg *
\****************************************************************************************** */
global $wprealty_tinymce_widget_version;
global $wprealty_tinymce_widget_dev_mode;
$wprealty_tinymce_widget_dev_mode = false;
/* Widget class */
class WP_Widget_WP_Realty extends WP_Widget {
function __construct() {
$widget_ops = array('classname' => 'widget_wprealty_tinymce', 'description' => __('Arbitrary text or HTML with visual editor', 'wp-realty-tinymce-widget'));
$control_ops = array('width' => 800, 'height' => 800);
parent::__construct('wp-realty-tinymce', __('WP Realty Widget', 'wp-realty-tinymce-widget'), $widget_ops, $control_ops);
}
function widget($args, $instance) {
if (get_option('embed_autourls')) {
$wp_embed = $GLOBALS['wp_embed'];
add_filter('widget_text', array($wp_embed, 'run_shortcode'), 8);
add_filter('widget_text', array($wp_embed, 'autoembed'), 8);
}
extract($args);
$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
$text = apply_filters('widget_text', $instance['text'], $instance);
if (function_exists('icl_t')) {
$title = icl_t("Widgets", 'widget title - ' . md5($title), $title);
$text = icl_t("Widgets", 'widget body - ' . $this->id_base . '-' . $this->number, $text);
}
$text = do_shortcode($text);
echo $before_widget;
if (!empty($title)) {
echo $before_title . $title . $after_title;
}
?>
<div class="textwidget"><?php echo $text; ?></div>
<?php
echo $after_widget;
}
function update($new_instance, $old_instance) {
$instance = $old_instance;
$instance['title'] = strip_tags($new_instance['title']);
if (current_user_can('unfiltered_html'))
$instance['text'] = $new_instance['text'];
else
$instance['text'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['text']))); // wp_filter_post_kses() expects slashed
$instance['type'] = strip_tags($new_instance['type']);
if (function_exists('icl_register_string')) {
icl_register_string("Widgets", 'widget body - ' . $this->id_base . '-' . $this->number /* md5 ( apply_filters( 'widget_text', $instance['text'] )) */, apply_filters('widget_text', $instance['text']));
}
return $instance;
}
function form($instance) {
$instance = wp_parse_args((array) $instance, array('title' => '', 'text' => '', 'type' => 'visual'));
$title = strip_tags($instance['title']);
if (function_exists('esc_textarea')) {
$text = esc_textarea($instance['text']);
} else {
$text = stripslashes(wp_filter_post_kses(addslashes($instance['text'])));
}
$type = esc_attr($instance['type']);
if (get_bloginfo('version') < "3.5") {
$toggle_buttons_extra_class = "editor_toggle_buttons_legacy";
$media_buttons_extra_class = "editor_media_buttons_legacy";
} else {
$toggle_buttons_extra_class = "wp-toggle-buttons";
$media_buttons_extra_class = "wp-media-buttons";
}
?>
<input id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" type="hidden" value="<?php echo esc_attr($type); ?>" />
<p>
<label for="<?php echo $this->get_field_id('title'); ?>">
<?php _e('Title:'); ?>
</label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
</p>
<div class="editor_toggle_buttons hide-if-no-js <?php echo $toggle_buttons_extra_class; ?>"> <a id="widget-<?php echo $this->id_base; ?>-<?php echo $this->number; ?>-html"<?php if ($type == 'html') { ?> class="active"<?php } ?>>
<?php _e('HTML'); ?>
</a><a id="widget-<?php echo $this->id_base; ?>-<?php echo $this->number; ?>-visual"<?php if ($type == 'visual') { ?> class="active"<?php } ?>>
<?php _e('Visual'); ?>
</a></div>
<div class="editor_media_buttons hide-if-no-js <?php echo $media_buttons_extra_class; ?>">
<?php do_action('media_buttons'); ?>
</div>
<div class="editor_container">
<textarea class="widefat" rows="20" cols="40" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
</div>
<?php
}
}
/* Widget initialization */
add_action('widgets_init', 'wprealty_tinymce_widgets_init');
function wprealty_tinymce_widgets_init() {
if (!is_blog_installed())
return;
register_widget('WP_Widget_WP_Realty');
}
/* Add actions and filters (only in widgets admin page) */
add_action('admin_init', 'wprealty_tinymce_admin_init');
function wprealty_tinymce_admin_init() {
global $pagenow;
$load_editor = false;
if ($pagenow == "widgets.php") {
$load_editor = true;
}
// Compatibility for WP Page Widget plugin
if (is_plugin_active('wp-page-widget/wp-page-widgets.php') && (
(in_array($pagenow, array('post-new.php', 'post.php'))) ||
(in_array($pagenow, array('edit-tags.php')) && isset($_GET['action']) && $_GET['action'] == 'edit') ||
(in_array($pagenow, array('admin.php')) && isset($_GET['page']) && in_array($_GET['page'], array('pw-front-page', 'pw-search-page')))
)) {
$load_editor = true;
}
if ($load_editor) {
add_action('admin_head', 'wprealty_tinymce_load_tiny_mce');
add_filter('tiny_mce_before_init', 'wprealty_tinymce_init_editor', 20);
add_action('admin_print_scripts', 'wprealty_tinymce_scripts');
add_action('admin_print_styles', 'wprealty_tinymce_styles');
add_action('admin_print_footer_scripts', 'wprealty_tinymce_footer_scripts');
}
}
/* Instantiate tinyMCE editor */
function wprealty_tinymce_load_tiny_mce() {
// Remove filters added from "After the deadline" plugin, to avoid conflicts
remove_filter('mce_external_plugins', 'add_AtD_tinymce_plugin');
remove_filter('mce_buttons', 'register_AtD_button');
remove_filter('tiny_mce_before_init', 'AtD_change_mce_settings');
// Add support for thickbox media dialog
add_thickbox();
// New media modal dialog (WP 3.5+)
if (function_exists('wp_enqueue_media')) {
wp_enqueue_media();
}
}
/* TinyMCE setup customization */
function wprealty_tinymce_init_editor($initArray) {
// Remove WP fullscreen mode and set the native tinyMCE fullscreen mode
if (get_bloginfo('version') < "3.3") {
$plugins = explode(',', $initArray['plugins']);
if (isset($plugins['wpfullscreen'])) {
unset($plugins['wpfullscreen']);
}
if (!isset($plugins['fullscreen'])) {
$plugins[] = 'fullscreen';
}
$initArray['plugins'] = implode(',', $plugins);
}
// Remove the "More" toolbar button
$initArray['theme_advanced_buttons1'] = str_replace(',wp_more', '', $initArray['theme_advanced_buttons1']);
// Do not remove linebreaks
$initArray['remove_linebreaks'] = false;
// Convert newline characters to BR tags
$initArray['convert_newlines_to_brs'] = false;
// Force P newlines
$initArray['force_p_newlines'] = true;
// Force P newlines
$initArray['force_br_newlines'] = false;
// Do not remove redundant BR tags
$initArray['remove_redundant_brs'] = false;
// Force p block
$initArray['forced_root_block'] = 'p';
// Apply source formatting
$initArray['apply_source_formatting '] = true;
// Return modified settings
return $initArray;
}
/* Widget js loading */
function wprealty_tinymce_scripts() {
global $wprealty_tinymce_widget_version, $wprealty_tinymce_widget_dev_mode;
wp_enqueue_script('media-upload');
if (get_bloginfo('version') >= "3.3") {
wp_enqueue_script('wplink');
wp_enqueue_script('wpdialogs-popup');
wp_enqueue_script('wp-realty-tinymce-widget', plugins_url('js/wpr-widget' . ($wprealty_tinymce_widget_dev_mode ? '.dev' : '') . '.js', __FILE__), array('jquery'), $wprealty_tinymce_widget_version);
} else {
print 'Older Versions No Longer Supported';
}
}
/* Widget css loading */
function wprealty_tinymce_styles() {
global $wprealty_tinymce_widget_version;
if (get_bloginfo('version') < "3.3") {
wp_enqueue_style('thickbox');
} else {
wp_enqueue_style('wp-jquery-ui-dialog');
}
wp_print_styles('editor-buttons');
wp_enqueue_style('wp-realty-tinymce-widget', plugins_url('css/wpr-widget.css', __FILE__), array(), $wprealty_tinymce_widget_version);
}
/* Footer script */
function wprealty_tinymce_footer_scripts() {
// Setup for WP 3.1 and previous versions
if (get_bloginfo('version') < "3.2") {
if (function_exists('wp_tiny_mce')) {
wp_tiny_mce(false, array());
}
if (function_exists('wp_tiny_mce_preload_dialogs')) {
wp_tiny_mce_preload_dialogs();
}
}
// Setup for WP 3.2.x
else if (get_bloginfo('version') < "3.3") {
if (function_exists('wp_tiny_mce')) {
wp_tiny_mce(false, array());
}
if (function_exists('wp_preload_dialogs')) {
wp_preload_dialogs(array('plugins' => 'wpdialogs,wplink,wpfullscreen'));
}
}
// Setup for WP 3.3 - New Editor API
else {
wp_editor('', 'wp-realty-tinymce-widget');
}
}
/* Hack needed to enable full media options when adding content form media library */
/* (this is done excluding post_id parameter in Thickbox iframe url) */
add_filter('_upload_iframe_src', 'wprealty_tinymce_upload_iframe_src');
function wprealty_tinymce_upload_iframe_src($upload_iframe_src) {
global $pagenow;
if ($pagenow == "widgets.php" || ($pagenow == "admin-ajax.php" && isset($_POST['id_base']) && $_POST['id_base'] == "wp-realty-tinymce")) {
$upload_iframe_src = str_replace('post_id=0', '', $upload_iframe_src);
}
return $upload_iframe_src;
}
/* Hack for widgets accessibility mode */
add_filter('wp_default_editor', 'wprealty_tinymce_editor_accessibility_mode');
function wprealty_tinymce_editor_accessibility_mode($editor) {
global $pagenow;
if ($pagenow == "widgets.php" && isset($_GET['editwidget']) && strpos($_GET['editwidget'], 'wp-realty-tinymce') === 0) {
$editor = 'html';
}
return $editor;
}
add_filter('no_texturize_shortcodes', 'shortcodes_to_exempt_from_wptexturize');
function shortcodes_to_exempt_from_wptexturize($shortcodes) {
$shortcodes[] = 'wprealty';
return $shortcodes;
}
// mm start
//add_filter( 'the_content', 'dash_to_hyphen' );
function dash_to_hyphen($content) {
//$content = str_replace("\xe2\x80\x93", '-', $content); // utf8
//$content = str_replace(chr(150), '-', $content); // 1252
str_replace("'", "\'", $content);
}
/*
//////////////////////////////////////////////////////////////////////////
// This section below allows for automatic updates - remove if unwanted //
//////////////////////////////////////////////////////////////////////////
//TEMP: Enable update check on every request. Normally you don't need this! This is for testing only!
// NOTE: The
//	if (empty($checked_data->checked))
//		return $checked_data;
// lines will need to be commented in the check_for_plugin_update function as well.
set_site_transient('update_plugins', null);
// TEMP: Show which variables are being requested when query plugin API
add_filter('plugins_api_result', 'aaa_result', 10, 3);
function aaa_result($res, $action, $args) {
print_r($res);
return $res;
}
// NOTE: All variables and functions will need to be prefixed properly to allow multiple plugins to be updated
*/
$api_url = 'http://retspro.com/api/';
$plugin_slug = basename(dirname(__FILE__));
// Take over the update check
add_filter('pre_set_site_transient_update_plugins', 'check_for_plugin_update');
function check_for_plugin_update($checked_data) {
global $api_url, $plugin_slug, $wp_version;
//Comment out these two lines during testing.
if (empty($checked_data->checked))
return $checked_data;
$args = array(
'slug' => $plugin_slug,
'version' => $checked_data->checked[$plugin_slug . '/' . $plugin_slug . '.php'],
);
$request_string = array(
'body' => array(
'action' => 'basic_check',
'request' => serialize($args),
'api-key' => md5(get_bloginfo('url'))
),
'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
);
// Start checking for an update
$raw_response = wp_remote_post($api_url, $request_string);
if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
$response = unserialize($raw_response['body']);
if (is_object($response) && !empty($response)) // Feed the update data into WP updater
$checked_data->response[$plugin_slug . '/' . $plugin_slug . '.php'] = $response;
return $checked_data;
}
// Take over the Plugin info screen
add_filter('plugins_api', 'plugin_api_call', 10, 3);
function plugin_api_call($def, $action, $args) {
global $plugin_slug, $api_url, $wp_version;
if (!isset($args->slug) || ($args->slug != $plugin_slug))
return false;
// Get the current version
$plugin_info = get_site_transient('update_plugins');
$current_version = $plugin_info->checked[$plugin_slug . '/' . $plugin_slug . '.php'];
$args->version = $current_version;
$request_string = array(
'body' => array(
'action' => $action,
'request' => serialize($args),
'api-key' => md5(get_bloginfo('url'))
),
'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
);
$request = wp_remote_post($api_url, $request_string);
if (is_wp_error($request)) {
$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
} else {
$res = unserialize($request['body']);
if ($res === false)
$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
}
return $res;
}
///////////////////////////////////
// End Check Auto-Update Option. //
///////////////////////////////////
?>