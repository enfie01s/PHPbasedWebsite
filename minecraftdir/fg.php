<?php
include '../conf.php';
$node = array();
$node[]=array('/fg handlers priority','foxguard.command.modify.objects.handlers.priority','Moderator');
$node[]=array('/fg modify','foxguard.command.modify.objects.modify','Moderator');
$node[]=array('/fg delete','foxguard.command.modify.objects.delete','Moderator');
$node[]=array('/fg detail','foxguard.command.info.detail','Moderator');
$node[]=array('/fg list','foxguard.command.info.list','Moderator');
$node[]=array('/fg here','foxguard.command.info.here','Moderator');
$node[]=array('/fg create','foxguard.command.modify.objects.create','Moderator');
$node[]=array('/fg enable','foxguard.command.modify.objects.enabledisable','Moderator');
$node[]=array('/fg disable','foxguard.command.modify.objects.enabledisable','Moderator');
$node[]=array('/fg link','foxguard.command.modify.link.link','Moderator');
$node[]=array('/fg rename','foxguard.command.modify.objects.rename','Moderator');
$node[]=array('/fg save','foxguard.command.save','Moderator');
$node[]=array('/fg unlink','foxguard.command.modify.link.unlink','Moderator');
$node[]=array('/fc about','foxcore.command.info.about','Moderator');
$node[]=array('/fc debug','foxcore.command.debug.debug','Admin');
$node[]=array('/fc flush','foxcore.command.state.flush','Moderator');
$node[]=array('/fc state','foxcore.command.state.state','Moderator');
$node[]=array('/fc current','foxcore.command.state.current','Moderator');
$node[]=array('/fc position','foxcore.command.state.add.position','Moderator');
$node[]=array('/fc test','foxcore.command.debug.test','Moderator');
$node[]=array('/fc wand','foxcore.command.wand','Moderator');
foreach($node as $arr){
  if($arr[0]!='NONE'){  
    $ranknum=array('Admin'=>1,'Moderator'=>3,'Assistant'=>9,'VIP'=>6,'Member'=>7,'default'=>10);
 $add = $db->prepare("INSERT INTO mc_plugperms(`pl`,`perm`,`bool`,`comm`,`descrip`,`mingroup`,`minrank`,`world`)VALUES('FoxGuard',?,'true',?,?,?,?,'All')");
  $add->execute(array($arr[1],$arr[0],'',trim($arr[2]),$ranknum[trim($arr[2])]));
}
}
?>