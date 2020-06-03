<?php
/* 
returns array of the current path.
eg: for aristia.net/shows/create/
$curpath = array('shows','create');
...$safeget defined in conf.php
*/
$curpath = isset($safeget['path'])?explode('/',rtrim($safeget['path'],'/')):array('main');

/* this will allow urls to be changed without affecting the directories */
$urls = array(
'main'=>'mainsitedir',
'shows'=>'showsdir',
'blog'=>'blogdir',
'portfolio'=>'portfoliodir',
'vegemites'=>'minecraftdir'
);

$rootpg = $curpath && array_key_exists($curpath[0],$urls)?$curpath[0]:'main';
$pagedir = $urls[$rootpg];
$templatesdir = "./".$pagedir."/templates/";
?>
