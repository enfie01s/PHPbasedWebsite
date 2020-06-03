<?php
include '../conf.php';
$node=array();
$node[]=array('None','','','griefprevention.claim.command.flags.base','Member');
$node[]=array('None','','','griefprevention.claim.flag','Member');
$node[]=array('None','','','griefprevention.claim.create','Member');
$node[]=array('None','','','griefprevention.claim.manage.flags','Admin');
$node[]=array('None','','','griefprevention.claim.flag.block-place','Member');
$node[]=array('None','','','griefprevention.claim.flag.interact-block-secondary','Member');
$node[]=array('None','','','griefprevention.claim.flag.interact-block-primary','Member');
$node[]=array('None','','','griefprevention.claim.command.flags.debug','Admin');
$node[]=array('None','','','griefprevention.claim.command.abandon','Member');
$node[]=array('None','','','griefprevention.claim.flag.block-break','Member');
$node[]=array('/restorenature','','Allows the use of the /restorenature command','griefprevention.restorenature','Admin');
$node[]=array('/restorenatureaggressive','','Allows the use of the /restorenatureaggressive command','griefprevention.restorenatureaggressive','Admin');
$node[]=array('None','','Players with this permission may place lava anywhere they have build permission, but still not near other players.','griefprevention.lava','VIP');
$node[]=array('None','','Allows creating, deleting and building in administrative claims','griefprevention.adminclaims','Admin');
$node[]=array('/claimslist','','Grants access to /claimslist command.','griefprevention.claimslistother','Admin');
$node[]=array('/ignoreclaims','','Allows the player to ignore claims using the /ignoreclaims command','griefprevention.ignoreclaims','Moderator');
$node[]=array('','','Allows the player to delete and resize other player\'s claims','griefprevention.deleteclaims','Admin');
$node[]=array('None','','Allows players to create claims with the claim creation tool (default shovel). All players have this permission by default.','griefprevention.createclaims','Member');
$node[]=array('/transferclaim','','Grants access to the /transferclaim command.','griefprevention.transferclaim','Admin');
$node[]=array('None','','This permission is granted to everyone by default, and is necessary for the claims-related slash commands.','griefprevention.claims','Member');
$node[]=array('None','','This permission is granted to everyone by default and allows the player the ability to buy/sell claim blocks','griefprevention.buysellclaimb­locks','Member');
$node[]=array('None','','Allows the player to adjust player\'s bonus claim blocks amount','griefprevention.adjustclaimblocks','Admin');
$node[]=array('None','','Allows the player to shift+right click with investigation tool (stick) in hand to see all nearby land claims.','griefprevention.visualizenearbyclaims','Member');
$node[]=array('None','','Allows the player to see private messages like /tell.','griefprevention.eavesdrop','Moderator');
$node[]=array('None','','Allows the player to receive notifications about newly-placed signs.','griefprevention.eavesdropsigns','Moderator');
$node[]=array('None','','Allows a player to bypass the total claim count limit set in your config file (defaults to no limit).','griefprevention.overrideclaimcountlimit','Moderator');
$node[]=array('None','','Allows the player to bypass the spam protection.','griefprevention.spam','Moderator');
$node[]=array('None','','Makes a player immune to the /siege command.','griefprevention.siegeimmune','VIP');
$node[]=array('None','','Makes a player immune to the /ignore command.','griefprevention.notignorable','Assistant');
$node[]=array('None','','Allows player to see how long a claim owner has been offline by using the claim investigation tool (default:stick).','griefprevention.seeinactivity','Member');
$node[]=array('None','','Allows player to see the size of another player\'s claim by using the claim investigation tool (default:stick)','griefprevention.seeclaimsize','Member');
$node[]=array('None','','Allows the player to teleport outside a player\'s claim when trapped and not trusted.','griefprevention.trapped','Member');

foreach($node as $arr){
    $ranknum=array('Admin'=>1,'Moderator'=>3,'Assistant'=>9,'VIP'=>6,'Member'=>7,'default'=>10);
 $add = $db->prepare("INSERT INTO mc_plugperms(`pl`,`perm`,`bool`,`comm`,`altcomm`,`descrip`,`mingroup`,`minrank`,`world`)VALUES('RedProtect',?,'true',?,?,?,?,?,'All')");
  $add->execute(array($arr[3],$arr[0],$arr[1],$arr[2],trim($arr[4]),$ranknum[trim($arr[4])]));

}
?>
