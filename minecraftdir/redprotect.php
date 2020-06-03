<?php
include '../conf.php';
$node=array();
$node[]=array('/rp tutorial','/rp tut','To show a tutorial on how to protect a region','','Member');
$node[]=array('/rp info [region] [world]','/rp i','To see all info about your region(for Owners)','redprotect.own.info','Member');
$node[]=array('None','/rp i','To see all info about your region(for Owners)','redprotect.infowand','Member');
$node[]=array('/rp list','/rp ls','To see all your regions','redprotect.own.list','Member');
$node[]=array('/rp delete','/rp del','To delete your region','redprotect.own.delete','Member');
$node[]=array('/rp border','/rp b','To show the border of region','redprotect.own.border','Member');
$node[]=array('/rp rename','/rp rn','To rename your region','redprotect.own.rename','Member');
$node[]=array('/rp claimlimit','/rp cl','See the claim limit','redprotect.own.claimlimit','Member');
$node[]=array('/rp limit','/rp lm','See block limit','redprotect.own.limit','Member');
$node[]=array('/rp addmember &lt;player&gt;','/rp am','To add a member in a region','redprotect.own.addmember','Member');
$node[]=array('/rp addadmin &lt;player&gt;','/rp aa','To promote a member as admin','redprotect.own.addadmin','Member');
$node[]=array('/rp addleader &lt;player&gt;','/rp al','(Send request...)To promote a admin or member as leader','redprotect.own.addleader','Member');
$node[]=array('/rp removemember &lt;player&gt;','/rp rm','To remove a player from region','redprotect.own.removemember','Member');
$node[]=array('/rp removeadmin &lt;player&gt;','/rp ra','To demote an admin to member','redprotect.own.removeadmin','Member');
$node[]=array('/rp removeleader &lt;player&gt;','/rp rl','To demote a player to admin from region','redprotect.own.removeleader','Member');
$node[]=array('/rp kick &lt;player&gt;','/rp k','To kick a player and temporary deny this player to enter on region','redprotect.own.kick','Member');
$node[]=array('/rp priority &lt;priority&gt;','/rp prior','Set the priority for child regions','redprotect.own.priority','Member');
$node[]=array('/rp flag info [region] [world]','/rp fl','To see the actual flags on your region','','Member');
$node[]=array('/rp flag','/rp fl','Open the flag GUI to change the flags','redprotect.own.flaggui','Member');
$node[]=array('/rp flag [region] &lt;FlagName&gt; [value] [world]','/rp fl','Change a flag state to true/false','','Member');
$node[]=array('/rp welcome &lt;message/off/hide&gt;','/rp wel','Set welcome message of region','redprotect.own.welcome','Member');
$node[]=array('/rp near','/rp nr','To see all regions near 40 blocks','redprotect.near','Member');
$node[]=array('/rp wand','/rp w','Get Magic Wand tool','redprotect.magicwand','Member');
$node[]=array('/rp define &lt;regionName&gt;','','Create new region with SelectionWand','redprotect.own.define','Member');
$node[]=array('/rp redefine &lt;regionName&gt;','','Redefine the area of a region with SelectionWand','redprotect.own.redefine','Member');
$node[]=array('/rp expand-vert [regionName] [world]','/rp ev','Expand the vertical size of region to 0 and max y allowed','redprotect.own.exoandvert','Member');
$node[]=array('/rp setmaxy [regionName] [world]','','Set the top vertical size of region','redprotect.own.setmaxy','Member');
$node[]=array('/rp setminy [regionName] [world]','','Set the min vertical size of region','redprotect.own.setminy','Member');
$node[]=array('/rp flag &lt;FlagName&gt; &lt;flag/remove&gt; &lt;value&gt;','/rp fl','Add or remove an admin flag','redprotect.admin.flag','Moderator');
$node[]=array('/rp flag gui-edit [lines-to-add]','/rp fl','Allow edit positions of FlagGui items','redprotect.gui.edit','Moderator');
$node[]=array('/rp teleport [player] &lt;regionName&gt; [World]','/rp tp','Teleport to region','redprotect.own.teleport','Member');
$node[]=array('/rp list &lt;player&gt; [page]','/rp ls','To see player regions','redprotect.admin.list','Assistant');
$node[]=array('/rp copyflag &lt;regionNameFrom&gt; &lt;regionNameTo&gt;','','To copy all flags from a region to other in same world','redprotect.admin.copyflag','Moderator');
$node[]=array('/rp setconfig list','','See all changeable configurations sections','redprotect.admin.setconfig','Moderator');
$node[]=array('/rp setconfig &lt;configSection&gt; &lt;value&gt;','','Change a specified config value','redprotect.admin.setconfig','Moderator');
$node[]=array('/rp reload','','Reload all configs and language file','redprotect.admin.reload','Admin');
$node[]=array('/rp reload-config','','Reload only the config and language','redprotect.admin.reload','Admin');
$node[]=array('/rp save-all','','To force save all new regions to database','redprotect.admin.save-all','Admin');
$node[]=array('/rp load-all','','To force load all regions from database','redprotect.admin.load-all','Admin');
$node[]=array('/rp regen','','Regen a region. <a href="https://github.com/FabioZumbi12/RedProtect/wiki/Commands#regen-command">Click here for more info!</a>','redprotect.regen','Admin');
$node[]=array('/rp undo','','Undo a regenerated region. <a href="https://github.com/FabioZumbi12/RedProtect/wiki/Commands#regen-command">Click here for more info!</a>','redprotect.regen','Admin');
$node[]=array('/rp regenall &lt;player&gt;','/rp regall','Regen all player regions','redprotect.regenall','Admin');
$node[]=array('/rp removeall &lt;player&gt;','/rp remall','Remove all player regions','redprotect.removeall','Admin');
/*
$node[]=array('/rp single-to-files','','Convert single world file to regions files','Only from console','Admin');
$node[]=array('/rp files-to-single','','Convert region files to single world files','Only from console','Admin');
$node[]=array('/rp list-all','','Try to list all regions on DB','Only from console');
$node[]=array('/rp mychunktorp','','Convert from MyChunk plugin to RP','Only from console');
$node[]=array('/rp gptorp','','Convert from GriefPrevention plugin to RP','Only from console');
$node[]=array('/rp ymlToMysql','','Transfer all data from yml to Mysql database','Only from console');
$node[]=array('/rp mysqlTpYml','','Transfer all data from Mysql to yml database','Only from console');
*/
$node[]=array('/rp buy','','To buy a region','redprotect.eco.buy','Member');
$node[]=array('/rp sell','','To sell your region','redprotect.eco.sell','Member');
$node[]=array('/rp sell &lt;value&gt;','','To sell your region with value','redprotect.eco.setvalue','Member');
$node[]=array('/rp sell &lt;player&gt; [value]','','To sell a in name of other player with value','redprotect.eco.others','Member');
$node[]=array('/rp cancelbuy','','Cancel selling your region','redprotect.eco.cancelbuy','Member');
$node[]=array('/rp value','','To calculate the value of region based on blocks','redprotect.admin','Assistant');

foreach($node as $arr){
    $ranknum=array('Admin'=>1,'Moderator'=>3,'Assistant'=>9,'VIP'=>6,'Member'=>7,'default'=>10);
 $add = $db->prepare("INSERT INTO mc_plugperms(`pl`,`perm`,`bool`,`comm`,`altcomm`,`descrip`,`mingroup`,`minrank`,`world`)VALUES('RedProtect',?,'true',?,?,?,?,?,'All')");
  $add->execute(array($arr[3],$arr[0],$arr[1],$arr[2],trim($arr[4]),$ranknum[trim($arr[4])]));

}
?>