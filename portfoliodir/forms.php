<?php
/* 
For options and radios, add 'options'=>array('option1','option2') 
*/
$postform = array(
    'title'=>array(
        'type'=>'text',
        'faclass'=>'info',
        'placeholder'=>'Title',
        'required'=>'required'),
    'seotitle'=>array(
        'type'=>'text',
        'faclass'=>'link',
        'placeholder'=>'Part of the url: path/<seotitle here>',
        'required'=>'required'),
    'pub_date'=>array(
        'type'=>'date',
        'faclass'=>'calendar',
        'placeholder'=>'Date of the event',
        'required'=>'required'),
    'image'=>array(
        'type'=>'file',
        'faclass'=>'picture-o',
        'placeholder'=>'Photo of the event',
        'required'=>''),
    'body'=>array(
        'type'=>'textarea',
        'faclass'=>'newspaper-o',
        'placeholder'=>'Main text',
        'required'=>'required'),
    );
?>
