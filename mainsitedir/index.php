<?php
$now = date("Y-m-d H:i:s");
$memdates = array(
    'Started dating'=>'2001-1-1 00:00:00',
    'Got married'=>'2004-6-19 00:00:00',
    'Mat quit smoking'=>'2013-9-27 21:00:00',
    'Mat quit vaping'=>'2018-7-1 11:00:00',
    'Christmas'=>date('Y').'-12-25 00:00:00'
);

$cards = array();
foreach($memdates as $title => $time){
    $cards[] = array(
        'title'=>$title,
        'waittime'=>datecompare($time,$now,2).($time < $now?"a":"to "),
        'date'=>$time
    );
}

$data['cards']=$cards;

$tpl->headers = array(
    'tpl_file'=>'home.html',
    'php_file'=>highlight_file('index.php',true),
);
$tpl->parse('home.html',$data);
?>