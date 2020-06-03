<?php
include '../conf.php';
$node = array();
$node[]=array('Ignore','/lp','Base LuckPerms command. Will print a list of the LuckPerms commands a user has permission to use, with brief information about what each command does, and what arguments it accepts.','');
$node[]=array('luckperms.sync','/lp sync','Performs a refresh of all currently loaded data. If any changes have been made to the data in the storage, this command will update the copy on the server to include those changes.','');
$node[]=array('luckperms.info','/lp info','Lists some information/data about LuckPerms, including debugging output, statistics, settings, and important values from the configuration.','');
$node[]=array('luckperms.verbose','/lp verbose','Controls the LuckPerms verbose logging system. This allows you to listen for all permission checks against players on the server. Whenever a permission is checked by a plugin, the check is passed onto the verbose handler.','');

$node[]=array('luckperms.tree','/lp tree','Generates a tree view of permissions registered to the server. The tree is built using data exposed to the server by plugins, and expanded over time as plugins check for permissions.','');
$node[]=array('luckperms.search','/lp search','Searches all users/groups for a specific permission, and returns a paginated list of all found entries.','');
$node[]=array('luckperms.check','/lp check','Performs a standard permission check on an online player, and returns the result. This check is equivalent to the checks performed by other plugins when checking for permissions.','');
$node[]=array('luckperms.sync','/lp networksync','Refreshes all cached data with the storage provider, and then uses the plugins Messaging Service (if configured) to "ping" all other connected servers and request that they sync too.','');
$node[]=array('luckperms.import','/lp import','Imports data into LuckPerms from a file. The file must be a list of commands, starting with "/luckperms". This file can be generated using the export command. The file is expected to be in the plugin directory.','');
$node[]=array('luckperms.export','/lp export','Exports data from LuckPerms into a file. This file can either be used as a backup, or used to move data between LuckPerms installations. The file can be re-imported using the import command. The generated file will be in the plugin directory.','');
$node[]=array('luckperms.reloadconfig','/lp reloadconfig','Reloads some values from the configuration file. Not all entries are reloaded by this command, and some require a full server reboot to take effect. (storage settings, for example)','');
$node[]=array('Ignore','/lp bulkupdate','Allows you to perform a bulk modifiction to all permission data. A detailed guide on how to use this command can be found <a href="https://github.com/lucko/LuckPerms/wiki/Bulk-Editing">here</a>.','');
$node[]=array('luckperms.migration','/lp migration','Main command used for the migration system. Allows you to import permissions data into LuckPerms from other permission plugins. More information about this feature can be found <a href="https://github.com/lucko/LuckPerms/wiki/Migration">here</a>.','');
$node[]=array('luckperms.creategroup','/lp creategroup','Creates a new group.','');
$node[]=array('luckperms.deletegroup','/lp deletegroup','Permanently deletes a group.','');
$node[]=array('luckperms.listgroups','/lp listgroups','Displays a list of all current groups.','');
$node[]=array('luckperms.createtrack','/lp createtrack','Creates a new track.','');
$node[]=array('luckperms.deletetrack','/lp deletetrack','Permanently deletes a track.','');
$node[]=array('luckperms.listtracks','/lp listtracks','Displays a list of all current tracks.','');

$node[]=array('luckperms.user.info','/lp user &lt;user&gt; info','Displays information about a user, including their username, primary group, parents, and current contexts.','');
$node[]=array('luckperms.user.editor','/lp user &lt;user&gt; editor','Opens a web interface to edit permissions for the specified group. After changes are saved, a command will be given that you need to run for the changes to take effect.','');
$node[]=array('luckperms.user.promote','/lp user &lt;user&gt; promote','This command will promote a user along a track. Firstly, the command will check to see if the user is on the track specified in the given contexts. If the user is not on the track, they will be added to the first group on the track. If they are on the track in more than one place, the command will fail. In all other cases, the user will be promoted up the track, and will be removed from the existing group. If the track action affects their primary group, that will be updated too.','');
$node[]=array('luckperms.user.demote','/lp user &lt;user&gt; demote','This command will demote a user along a track. Firstly, the command will check to see if the user is on the track specified in the given contexts. If the user is not on the track, or on the track in more than one place, the command will fail. If not, the user will be demoted down the track, and will be removed from the existing group. If the track action affects their primary group, that will be updated too.','');
$node[]=array('luckperms.user.showtracks','/lp user &lt;user&gt; showtracks','Displays a list of all of the tracks a user is currently on.','');
$node[]=array('luckperms.user.clear','/lp user &lt;user&gt; clear','Clears the user\'s permissions, parent groups and meta.','');

$node[]=array('luckperms.group.info','/lp group &lt;group&gt; info','Displays information about a group.','');
$node[]=array('luckperms.group.editor','/lp group &lt;group&gt; editor','Opens a web interface to edit permissions for the specified group. After changes are saved, a command will be given that you need to run for the changes to take effect.','');
$node[]=array('luckperms.group.listmembers','/lp group &lt;group&gt; listmembers','Gets a list of the other users/groups which inherit directly from this group.','');
$node[]=array('luckperms.group.setweight','/lp group &lt;group&gt; setweight','Sets the groups weight value, which determines the order in which groups will be considered when accumulating a users permissions. Higher value = higher weight.','');
$node[]=array('luckperms.group.showtracks','/lp group &lt;group&gt; showtracks','Displays a list of all of the tracks a group is currently on.','');
$node[]=array('luckperms.group.clear','/lp group &lt;group&gt; clear','Clears the group\'s permissions, parent groups and meta.','');
$node[]=array('luckperms.group.rename','/lp group &lt;group&gt; rename','Changes a group\'s name. Note that any members of this group will not know about the change, and will still point to the old group name. If you wish to update this, you\'ll need to use the bulk change feature to update the existing entries.','');
$node[]=array('luckperms.group.clone','/lp group &lt;group&gt; clone','Makes an exact copy of the group under a different name.','');

$node[]=array('luckperms.user.permission.info','/lp user &lt;user&gt; permission info','Displays a list of the permission nodes a user/group has.','');
$node[]=array('luckperms.group.permission.info','/lp group &lt;group&gt; permission info','Displays a list of the permission nodes a user/group has.','');
$node[]=array('luckperms.user.permission.set','/lp user &lt;user&gt;permission set','Sets (or gives) a permission for a user. Providing a value of "false" will negate the permission.','');
$node[]=array('luckperms.group.permission.set','/lp group &lt;group&gt;permission set','Sets (or gives) a permission for a group. Providing a value of "false" will negate the permission.','');
$node[]=array('luckperms.user.permission.unset','/lp user &lt;user&gt;permission unset','Unsets (or removes) a permission for a user.','');
$node[]=array('luckperms.group.permission.unset','/lp group &lt;group&gt;permission unset','Unsets (or removes) a permission for a group.','');
$node[]=array('luckperms.user.permission.settemp','/lp user &lt;user&gt;permission settemp','Sets a permission temporarily for a user. Providing a value of "false" will negate the permission. Duration should either be a time period, or a unix timestamp when the permission will expire. e.g. "3d13h45m" will set the permission to expire in 3 days, 13 hours and 45 minutes time. "1482694200" will set the permission to expire at 7:30PM on 25th December 2016.','');
$node[]=array('luckperms.group.permission.settemp','/lp group &lt;group&gt;permission settemp','Sets a permission temporarily for a group. Providing a value of "false" will negate the permission. Duration should either be a time period, or a unix timestamp when the permission will expire. e.g. "3d13h45m" will set the permission to expire in 3 days, 13 hours and 45 minutes time. "1482694200" will set the permission to expire at 7:30PM on 25th December 2016.','');
$node[]=array('luckperms.user.permission.unsettemp','/lp user &lt;user&gt;permission unsettemp','Unsets a temporary permission for a user.','');
$node[]=array('luckperms.group.permission.unsettemp','/lp group &lt;group&gt;permission unsettemp','Unsets a temporary permission for a group.','');
$node[]=array('luckperms.user.permission.check','/lp user &lt;user&gt;permission check','Checks to see if a user has a certain permission.','');
$node[]=array('luckperms.group.permission.check','/lp group &lt;group&gt;permission check','Checks to see if a group has a certain permission.','');
$node[]=array('luckperms.user.permission.checkinherits','/lp user &lt;user&gt;permission checkinherits','Checks to see if a user inherits a certain permission, and if so, where from.','');
$node[]=array('luckperms.group.permission.checkinherits','/lp group &lt;group&gt;permission checkinherits','Checks to see if a group inherits a certain permission, and if so, where from.','');
$node[]=array('luckperms.user.parent.info','/lp user &lt;user&gt;parent info','Displays a list of a user\'s parent groups. (groups they inherit from)','');
$node[]=array('luckperms.group.parent.info','/lp group &lt;group&gt;parent info','Displays a list of a group\'s parent groups. (groups they inherit from)','');
$node[]=array('luckperms.user.parent.set','/lp user &lt;user&gt;parent set','Sets a user\'s parent. Unlike the "parent add" command, this command will clear all existing groups set at the given context. The add command will simply "add" the group to the existing ones a user/group has. If the command is executed with no context arguments, this command will also update a user\'s primary group.','');
$node[]=array('luckperms.group.parent.set','/lp group &lt;group&gt;parent set','Sets a group\'s parent. Unlike the "parent add" command, this command will clear all existing groups set at the given context. The add command will simply "add" the group to the existing ones a user/group has. If the command is executed with no context arguments, this command will also update a user\'s primary group.','');
$node[]=array('luckperms.user.parent.add','/lp user &lt;user&gt;parent add','Adds a parent to a user. Unlike the "parent set" command, this command will just accumulate the given parent with the ones the user/group already has. No existing parents will be removed from the user, and a user\'s primary group will be unaffected.','');
$node[]=array('luckperms.group.parent.add','/lp group &lt;group&gt;parent add','Adds a parent to a group. Unlike the "parent set" command, this command will just accumulate the given parent with the ones the user/group already has. No existing parents will be removed from the user, and a user\'s primary group will be unaffected.','');
$node[]=array('luckperms.user.parent.remove','/lp user &lt;user&gt;parent remove','Removes a parent from the user.','');
$node[]=array('luckperms.group.parent.remove','/lp group &lt;group&gt;parent remove','Removes a parent from the group.','');
$node[]=array('luckperms.user.parent.settrack','/lp user/group &lt;user|group&gt; parent settrack','Sets a users/groups position on a given track. This behaves in the same way as the set command, except it only clears existing groups which are on the specified track. Other parent groups are not affected.','');
$node[]=array('luckperms.group.parent.settrack','/lp user/group &lt;user|group&gt; parent settrack','Sets a users/groups position on a given track. This behaves in the same way as the set command, except it only clears existing groups which are on the specified track. Other parent groups are not affected.','');
$node[]=array('luckperms.user.parent.addtemp','/lp user &lt;user&gt;parent addtemp','Adds a parent to a user temporarily. Duration should either be a time period, or a unix timestamp when the permission will expire. e.g. "3d13h45m" will set the permission to expire in 3 days, 13 hours and 45 minutes time. "1482694200" will set the permission to expire at 7:30PM on 25th December 2016.','');
$node[]=array('luckperms.group.parent.addtemp','/lp group &lt;group&gt;parent addtemp','Adds a parent to a group temporarily. Duration should either be a time period, or a unix timestamp when the permission will expire. e.g. "3d13h45m" will set the permission to expire in 3 days, 13 hours and 45 minutes time. "1482694200" will set the permission to expire at 7:30PM on 25th December 2016.','');
$node[]=array('luckperms.user.parent.removetemp','/lp user &lt;user&gt;parent removetemp','Removes a tempoary parent from the user.','');
$node[]=array('luckperms.group.parent.removetemp','/lp group &lt;group&gt;parent removetemp','Removes a tempoary parent from the group.','');
$node[]=array('luckperms.user.parent.clear','/lp user/group &lt;user|group&gt; parent clear','Removes all parents.','');
$node[]=array('luckperms.group.parent.clear','/lp user/group &lt;user|group&gt; parent clear','Removes all parents.','');
$node[]=array('luckperms.user.parent.cleartrack','/lp user &lt;user&gt;parent cleartrack','Removes all parents from the user on a given track.','');
$node[]=array('luckperms.group.parent.cleartrack','/lp group &lt;group&gt;parent cleartrack','Removes all parents from the group on a given track.','');
$node[]=array('luckperms.user.parent.switchprimarygroup','/lp user &lt;user&gt; parent switchprimarygroup','This command allows you to change a user\'s primary group. If they are not already a member of the specified group, they will be added to it. This should not be used as a replacement to the "parent set" command. Their existing primary group will not be removed as a parent. (a user can have multiple parent groups)','');

$node[]=array('luckperms.user.meta.info','/lp user &lt;user&gt;meta info','Displays a list of a user\'s inherited meta (options), prefixes and suffixes.','');
$node[]=array('luckperms.group.meta.info','/lp group &lt;group&gt;meta info','Displays a list of a group\'s inherited meta (options), prefixes and suffixes.','');
$node[]=array('luckperms.user.meta.set','/lp user &lt;user&gt;meta set','Sets a meta key value pair for a user. These values can be read and modified by other plugins using Vault or the Sponge Permissions API.','');
$node[]=array('luckperms.group.meta.set','/lp group &lt;group&gt;meta set','Sets a meta key value pair for a group. These values can be read and modified by other plugins using Vault or the Sponge Permissions API.','');
$node[]=array('luckperms.user.meta.unset','/lp user &lt;user&gt;meta unset','Unsets a meta key value pair for a user.','');
$node[]=array('luckperms.group.meta.unset','/lp group &lt;group&gt;meta unset','Unsets a meta key value pair for a group.','');
$node[]=array('luckperms.user.meta.settemp','/lp user/group &lt;usergroup&gt; meta settemp','Sets a temporary meta key value pair for a user/group. Duration should either be a time period, or a unix timestamp when the permission will expire. e.g. "3d13h45m" will set the permission to expire in 3 days, 13 hours and 45 minutes time. "1482694200" will set the permission to expire at 7:30PM on 25th December 2016.','');
$node[]=array('luckperms.group.meta.settemp','/lp user/group &lt;usergroup&gt; meta settemp','Sets a temporary meta key value pair for a user/group. Duration should either be a time period, or a unix timestamp when the permission will expire. e.g. "3d13h45m" will set the permission to expire in 3 days, 13 hours and 45 minutes time. "1482694200" will set the permission to expire at 7:30PM on 25th December 2016.','');
$node[]=array('luckperms.user.meta.unsettemp','/lp user &lt;user&gt;meta unsettemp','Unsets a temporary meta key value pair for a user.','');
$node[]=array('luckperms.group.meta.unsettemp','/lp group &lt;group&gt;meta unsettemp','Unsets a temporary meta key value pair for a group.','');
$node[]=array('luckperms.user.meta.addprefix','/lp user &lt;user&gt;meta addprefix','Adds a prefix to a user. You can wrap the prefix in " " quotes to escape spaces.','');
$node[]=array('luckperms.group.meta.addprefix','/lp group &lt;group&gt;meta addprefix','Adds a prefix to a group. You can wrap the prefix in " " quotes to escape spaces.','');
$node[]=array('luckperms.user.meta.addsuffix','/lp user &lt;user&gt;meta addsuffix','Adds a suffix to a user. You can wrap the suffix in " " quotes to escape spaces.','');
$node[]=array('luckperms.group.meta.addsuffix','/lp group &lt;group&gt;meta addsuffix','Adds a suffix to a group. You can wrap the suffix in " " quotes to escape spaces.','');
$node[]=array('luckperms.user.meta.removeprefix','/lp user &lt;user&gt;meta removeprefix','Removes a prefix from a user. You can wrap the prefix in " " quotes to escape spaces.','');
$node[]=array('luckperms.group.meta.removeprefix','/lp group &lt;group&gt;meta removeprefix','Removes a prefix from a group. You can wrap the prefix in " " quotes to escape spaces.','');
$node[]=array('luckperms.user.meta.removesuffix','/lp user &lt;user&gt;meta removesuffix','Removes a suffix from a user. You can wrap the suffix in " " quotes to escape spaces.','');
$node[]=array('luckperms.group.meta.removesuffix','/lp group &lt;group&gt;meta removesuffix','Removes a suffix from a group. You can wrap the suffix in " " quotes to escape spaces.','');
$node[]=array('luckperms.user.meta.addtempprefix','/lp user &lt;user&gt;meta addtempprefix','Adds a prefix to a user temporarily. You can wrap the prefix in " " quotes to escape spaces. Duration should either be a time period, or a unix timestamp when the permission will expire. e.g. "3d13h45m" will set the permission to expire in 3 days, 13 hours and 45 minutes time. "1482694200" will set the permission to expire at 7:30PM on 25th December 2016.','');
$node[]=array('luckperms.group.meta.addtempprefix','/lp group &lt;group&gt;meta addtempprefix','Adds a prefix to a group temporarily. You can wrap the prefix in " " quotes to escape spaces. Duration should either be a time period, or a unix timestamp when the permission will expire. e.g. "3d13h45m" will set the permission to expire in 3 days, 13 hours and 45 minutes time. "1482694200" will set the permission to expire at 7:30PM on 25th December 2016.','');
$node[]=array('luckperms.user.meta.addtempsuffix','/lp user &lt;user&gt;meta addtempsuffix','Adds a suffix to a user temporarily. You can wrap the suffix in " " quotes to escape spaces. Duration should either be a time period, or a unix timestamp when the permission will expire. e.g. "3d13h45m" will set the permission to expire in 3 days, 13 hours and 45 minutes time. "1482694200" will set the permission to expire at 7:30PM on 25th December 2016.','');
$node[]=array('luckperms.group.meta.addtempsuffix','/lp group &lt;group&gt;meta addtempsuffix','Adds a suffix to a group temporarily. You can wrap the suffix in " " quotes to escape spaces. Duration should either be a time period, or a unix timestamp when the permission will expire. e.g. "3d13h45m" will set the permission to expire in 3 days, 13 hours and 45 minutes time. "1482694200" will set the permission to expire at 7:30PM on 25th December 2016.','');
$node[]=array('luckperms.user.meta.removetempprefix','/lp user &lt;user&gt;meta removetempprefix','Removes a tempoary prefix from a user. You can wrap the prefix in " " quotes to escape spaces.','');
$node[]=array('luckperms.group.meta.removetempprefix','/lp group &lt;group&gt;meta removetempprefix','Removes a tempoary prefix from a group. You can wrap the prefix in " " quotes to escape spaces.','');
$node[]=array('luckperms.user.meta.removetempsuffix','/lp user &lt;user&gt;meta removetempsuffix','Removes a temporary suffix from a user. You can wrap the suffix in " " quotes to escape spaces.','');
$node[]=array('luckperms.group.meta.removetempsuffix','/lp group &lt;group&gt;meta removetempsuffix','Removes a temporary suffix from a group. You can wrap the suffix in " " quotes to escape spaces.','');
$node[]=array('luckperms.user.meta.clear','/lp user/group &lt;user|group&gt; meta clear','Removes all meta/prefixes/suffixes.','');
$node[]=array('luckperms.group.meta.clear','/lp user/group &lt;user|group&gt; meta clear','Removes all meta/prefixes/suffixes.','');

$node[]=array('luckperms.track.info','/lp track &lt;track&gt; info','Displays the groups in the track.','');
$node[]=array('luckperms.track.info','/lp track &lt;track&gt; append','Adds a group onto the end of the track.','');
$node[]=array('luckperms.track.insert','/lp track &lt;track&gt; insert','Inserts a group into a specific position within this track. A position of 1 would place it at the start of the track.','');
$node[]=array('luckperms.track.remove','/lp track &lt;track&gt; remove','Removes a group from the track.','');
$node[]=array('luckperms.track.clear','/lp track &lt;track&gt; clear','Removes all groups from the track.','');
$node[]=array('luckperms.track.rename','/lp track &lt;track&gt; rename','Changes a track\'s name.','');
$node[]=array('luckperms.track.clone','/lp track &lt;track&gt; clone','Makes an exact copy of the track under a different name.','');

$node[]=array('luckperms.log.recent','/lp log recent','Shows a list of recent actions.','');
$node[]=array('luckperms.log.search','/lp log search','Searches for log entries matching the given query.','');
$node[]=array('luckperms.log.notify','/lp log notify','Toggles log notifications for the sender executing the command.','');
$node[]=array('luckperms.log.export','/lp log export','Exports the log to a list of commands, recognisable by the "/lp import" command. This feature should rarely be used, and use of "/lp export" is reccomended instead.','');
$node[]=array('luckperms.log.userhistory','/lp log userhistory','Searches for log entries acting upon the given user.','');
$node[]=array('luckperms.log.grouphistory','/lp log grouphistory','Searches for log entries acting upon the given group.','');
$node[]=array('luckperms.log.trackhistory','/lp log trackhistory','Searches for log entries acting upon the given track.','');

foreach($node as $arr){
  if($arr[0]!='Ignore'){
  if(stripos($arr[1],'info') !== false || stripos($arr[1],'check') !== false || stripos($arr[1],'search') !== false){
    $rank= 'Member';
    $ranknum=7;}
  else{
    $rank='Admin';
    $ranknum=1;
  }
  $add = $db->prepare("INSERT INTO mc_plugperms(`pl`,`perm`,`bool`,`comm`,`descrip`,`mingroup`,`minrank`,`world`)VALUES('LuckPerms',?,'true',?,?,?,?,'All')");
  $add->execute(array($arr[0],$arr[1],$arr[2],$rank,$ranknum));
}
}
?>