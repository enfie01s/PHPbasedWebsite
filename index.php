<?php
session_name('arisite');
session_start();
date_default_timezone_set("Europe/London");
include 'conf.php';
include 'urls.php';
if(is_file($pagedir.'/config.php')){include $pagedir.'/config.php';}
if(is_file($pagedir.'/functions.php')){include $pagedir.'/functions.php';}
include 'functions.php';
if(is_file($pagedir.'/forms.php')){include $pagedir.'/forms.php';}

$tpl = new Puja;
$tpl->template_dirs = array('templates/',$templatesdir);
$tpl->cache_dir = '../cache/';
$tpl->cache_level = 0;
$tpl->parse_executer = 'eval';
$tpl->debug = true;
$tpl->custom_filter = new CustomFilter;
$tpl->headers = array(
    #'tpl_file'=>strlen($pagedir)>0?'home.html':'',
    'php_file'=>highlight_file('index.php',true),
);

if(0&&$_SERVER['HTTP_HOST']!='local.ari'&&!isset($safeget['debug']))
{error_reporting(0);} // Disable all errors.

$data = array(
    'loggedin'=>$loggedin,
    'pagedir'=>$pagedir,
    'rootpg'=>$rootpg,
    'csrf_token'=>$csrf_token,
    'copydate'=>date('Y')
);

if(strlen($pagedir)>0){
    include $pagedir."/index.php";
}
?>
