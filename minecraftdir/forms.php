<?php
/* 
For options and radios, add 'options'=>array('option1','option2') 
mc_plugperms
*/

$optsQ = $db->query("SELECT GROUP_CONCAT(DISTINCT `pl`) AS plugins, GROUP_CONCAT(DISTINCT `rank` ORDER BY t2.`order`) AS ranks, GROUP_CONCAT(DISTINCT t2.`id` ORDER BY t2.`order`) AS minranks FROM mc_plugperms as t1 JOIN mc_ranks as t2 ON t1.`minrank`=t2.`id` WHERE `pl` IN (".$availplugs.",'default')");
$opts = $optsQ->fetchAll(PDO::FETCH_ASSOC);

$commandsform = array(
    'pl'=>array(
        'type'=>'select',
        'options'=>explode(",",$opts[0]['plugins']),
        'class'=>'col-sm-5',
        'faclass'=>'cube',
        'placeholder'=>'Plugin',
        'required'=>''),
    'minrank'=>array(
        'type'=>'select',
        'options'=>explode(",",$opts[0]['minranks']),
        'options_vals'=>explode(",",$opts[0]['ranks']),
        'class'=>'col-sm-5',
        'faclass'=>'id-badge',
        'placeholder'=>'Rank',
        'required'=>''),
    );
?>