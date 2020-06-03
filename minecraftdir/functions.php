<?php
function prettycolours($string){
    global $colours;
    $pref = '<span>' . $string . '</span>';
    foreach($colours as $k=>$v){
        $pref = str_ireplace('&'.$k, '</span><span style=\'color:'. $v .'\'>',$pref);
    }
    return $pref;
}

function grids($itemid){
    global $itemdata,$itempos;
    $data = $itemdata[$itemid];
    $iinpos = array_key_exists($itemid,$itempos);
    $posL = $iinpos ? $itempos[$itemid][0] : '0';
    $posT = $iinpos ? $itempos[$itemid][1] : '0';
    $itype = 'itemimg';
    if (substr($itemid,0,4) == '373_'){
        $itype='itempotion';
    }
    $name = $data[0];
    $states = $data[1];
    $grid = '';
    $returngrid = '';
    $gridtype = '';
    $itemsmade = 1;
    if (count($data) > 3){
        $gridlist = $data[3];
        if (count($gridlist) > 0){
            $gridtype = $gridlist[0];
            if( $gridtype == 'craft'){
                $itemsmade = $gridlist[10];
                $recipe = array_slice($gridlist,1,10);
            }
            else{
                $recipe = array_slice($gridlist,1);
            }
            if (count($recipe) > 0){
                foreach($recipe as $bit){
                    if (strlen($bit) > 0){
                        $btype = 'itemimg';
                        if(substr($bit,0,4)=='373_'){
                            $btype='itempotion';
                        }
                        $returngrid .= $bit . '/' . (array_key_exists($bit,$itempos) ? $itempos[$bit][0] . '+' . $itempos[$bit][1] : '0+0') . '/' . $btype;
                    }
                    $returngrid .= ',';
                }
            }
        }
    }
    $grid = array($name,$gridtype,$itemsmade,$returngrid,$posL,$posT,$itype);
    return $grid;
}

function insert_prep($table,$data){
    global $db;
    $insert = $db->prepare("INSERT INTO ".$table." (`".implode("`,`",$data)."`) VALUES(?".str_repeat(",?",count($data)-1).")");
    return $insert;
}

function kits_to_nu(){
    global $kits;
    $kitstring = '';
    $space = '  ';
    $newline = PHP_EOL;
    $totalkits = count($kits);
    $k=0;
    foreach($kits as $kit => $details) {
        $kitstring .= $newline . $space . '"' . $kit . '"' . ': {';//  "diamond": {
        $kitstring .= $newline . $space . $space . $space . '"ignoresPermission": false,';
        $kitstring .= $newline . $space . $space . $space . '"displayMessage": true,';
        $kitstring .= $newline . $space . $space . $space . '"cost": ' . $details[0] . '.0' . ',';
        $kitstring .= $newline . $space . $space . $space . '"firstJoin": false,';
        $kitstring .= $newline . $space . $space . $space . '"hidden": false,';
        $kitstring .= $newline . $space . $space . $space . 'stacks: [';
        $totalitems = count($details[2]);
        $n=0;
        foreach($details[2] as $detail => $extra) {
            $item_qty = explode("-",$detail);
            $kitstring .= $newline . $space . $space . $space . $space . '{';

            $kitstring .= $newline . $space . $space . $space . $space . $space . '"ContentVersion": 1,';// "ContentVersion": 1,
            $kitstring .= $newline . $space . $space . $space . $space . $space . '"ItemType": "minecraft:' . $item_qty[0] . '",';// "ItemType": "minecraft:diamond_pickaxe",
            $kitstring .= $newline . $space . $space . $space . $space . $space . '"Count": "' . $item_qty[1] . '",';// "Count": 1,
            $kitstring .= $newline . $space . $space . $space . $space . $space . '"UnsafeDamage": 0';// "UnsafeDamage": 1,
            $totalextra = count($extra);
            if($totalextra>0) {
                $kitstring .= $newline . $space . $space . $space . $space . $space . '"UnsafeData": {';
                $kitstring .= $newline . $space . $space . $space . $space . $space . $space . '"ench": [';
            }
            $e=0;
            foreach($extra as $atr => $val){
                if($atr != 'name' && $atr != 'lore'){
                    $kitstring .= $newline . $space . $space . $space . $space . $space . $space . $space . '{';
                    $kitstring .= $newline . $space . $space . $space . $space . $space . $space . $space . $space . '"lvl": ' . (string)$val . ',';// efficiency:5 unbreaking:3 fortune:3 
                    $kitstring .= $newline . $space . $space . $space . $space . $space . $space . $space . $space . '"id": ' . $atr;
                    $kitstring .= $newline . $space . $space . $space . $space . $space . $space . $space . '}' . ($e < $totalextra-3 ? ',' : '');
                    $e++;
                }
            }
            if($totalextra>0) {
                $kitstring .= $newline . $space . $space . $space . $space . $space . $space . ']';
                if(isset($extra['name'])||isset($extra['lore'])){
                    $kitstring .= ',' . $newline . $space . $space . $space . $space . $space . $space . '"display": {';
                    if(isset($extra['lore'])){
                        $kitstring .= ',' . $newline . $space . $space . $space . $space . $space . $space . $space . '"Lore": [';
                        $kitstring .= $newline . $space . $space . $space . $space . $space . $space . $space . $space . '"' . $extra['lore'] . '"';//Armour of the Vegemite gods.
                        $kitstring .= $newline . $space . $space . $space . $space . $space . $space . $space . ']';
                    }
                    if(isset($extra['name'])){
                        $kitstring .= ',' . $newline . $space . $space . $space . $space . $space . $space . $space . '"Name": "' . $extra['name'] . '"';//name:&4MegaPick lore:Tool
                    }
                }
                $kitstring .= $newline . $space . $space . $space . $space . $space . '}';
            }
            $kitstring .= $newline . $space . $space . $space . $space . '}' . ($n < $totalitems-1 ? ',' : '');
            $n++;
        }
        $kitstring .= $newline . $space . $space . $space . '],';
        $kitstring .= $newline . $space . $space . $space . '"autoRedeem": false,';
        $kitstring .= $newline . $space . $space . $space . '"interval": ' . $details[1] . ',';
        $kitstring .= $newline . $space . $space . $space . '"oneTime": false,';
        $kitstring .= $newline . $space . $space . $space . '"commands": []';
        $kitstring .= $newline . $space . $space . '}' . ($k < $totalkits-1 ? ',' : '');
        $k++;
    }
    $kitstring = '{' . $newline . '"kits": {' . $kitstring . $newline . $space . '}' . $newline . '}';
    return $kitstring;
}

function kits_to_ess(){
    global $kits, $itemdata;
    $kitstring = '';
    $space = '  ';
    $newline = PHP_EOL;
    foreach($kits as $kit => $details){
        /*
        
         "wood"=>array(0,1800,array("270-1"=>array(),"271-1"=>array(),"269-1"=>array(),"268-1"=>array(),"290-1"=>array(),"298-1"=>array(),"50-1"=>array(),"280-6"=>array()),"All"),

         $kit="wood"
         $details = array(0,1800,array("270-1"=>array(),"271-1"=>array(),"269-1"=>array(),"268-1"=>array(),"290-1"=>array(),"298-1"=>array(),"50-1"=>array(),"280-6"=>array()),"All")

        */
        if(strlen($kitstring) > 0){
            $kitstring .= $newline;
        }
        $kitstring .= $space . $kit . ':';//  protect:
        $kitstring .= $newline . $space . $space . 'delay: ' . (string)$details[1];// delay: 86400
        $kitstring .= $newline . $space . $space . 'items:';
        foreach($details[2] as $detail => $extra){
            /*
            $item_qty[0] = item id 284:0
             */
            $item_qty = explode("-",$detail);
            $item_id = str_ireplace(':','_',$item_qty[0]);
            $item_name = strtolower(str_ireplace(' ','_',$itemdata[$item_id][0]));
            $kitstring .= $newline . $space . $space . $space . '- ' . $item_name . ' ' . $item_qty[1];// - 284:0 1
            foreach($extra as $atr => $val){
                $kitstring .= ' ' . $atr . ':' . (string)$val;// efficiency:5 unbreaking:3 fortune:3 name:&4MegaPick lore:Tool
            }
        }
    }
    return $kitstring;
}
function prettyprefix($prefix){
    global $colours;
    $pref = '<span>' . $prefix . '</span>';
    foreach($colours as $k => $v){
        $pref = str_ireplace('&'.$k, '</span><span style=\'color:'. $v .'\'>',$pref);
    }
    return $pref;
}
if(isset($safeget['luckperms'])){perms_to_luck();}
function perms_to_luck(){
    global $db,$availplugs;

    $luck_grp = insert_prep('luckperms_groups',array('name'));
    $luck_grp_perms = insert_prep('luckperms_group_permissions',array('name','permission','value','server','world','expiry','contexts'));
    $luck_usr_perms = insert_prep('luckperms_user_permissions',array('uuid','permission','value','server','world','expiry','contexts'));
    $luck_player = insert_prep('luckperms_players',array('uuid','username','primary_group'));
/*
Clear Tables
*/
    $db->query("TRUNCATE TABLE luckperms_group_permissions");
    $db->query("TRUNCATE TABLE luckperms_groups");
    $db->query("TRUNCATE TABLE luckperms_players");
    $db->query("TRUNCATE TABLE luckperms_user_permissions");

/*
Main Permissions
*/
    $permsQ = $db->query("SELECT * FROM mc_plugperms as t1 JOIN mc_ranks as t2 ON t1.minrank=t2.id WHERE `perm` IS NOT NULL AND CHAR_LENGTH(`perm`) >0 AND `pl` IN (".$availplugs.",'default') AND `comm` != 'ignore' ORDER BY `perm`,`minrank`,`world`");//# Site defined permissions except invividual perms and system rows
    $lastcombo = '';
    while($permi = $permsQ->fetch(PDO::FETCH_ASSOC)){
        $thiscombo = $permi['perm'].'-'.$permi['rank'].'-'.$permi['world'];
        if($lastcombo != $thiscombo && strlen($permi['perm']) > 0 && substr($permi['perm'],-5) != '.NAME'){
            $lp=array();
            $lp['name'] = strtolower($permi['rank']);
            $lp['permission'] = $permi['perm'];
            $lp['value'] = ($permi['bool'] == 'false' ? '0'  : '1');
            $lp['server'] = 'global';
            $lp['world'] =  /*$permi['world'] != 'All' && $permi['world'] != null ? $permi['world'] : */'global';
            //setting to global as unsure what the correct worldnames are.
            $lp['expiry'] = '0';
            $lp['contexts']='{}';
            $luck_grp_perms->execute(array_values($lp));
        }
        $lastcombo = $thiscombo;
    }
/*
Users & Ranks
*/
    $usersQ = $db->query("SELECT *,t2.rank as rank FROM mc_players as t1 JOIN mc_ranks as t2 ON t1.rank=t2.id");// Site defined users
    $ranksQ = $db->query("SELECT * FROM mc_ranks ORDER BY `order`");// Site defined ranks

    while($user = $usersQ->fetch(PDO::FETCH_ASSOC)){
        $lpp=array();
        $lpp['uuid'] = $user['uuid'];
        $lpp['username'] = $user['player'];
        $lpp['primary_group'] = strtolower($user['rank']);
        $luck_player->execute(array_values($lpp));

        $lpu=array();
        $lpu['uuid'] = $user['uuid'];
        $lpu['permission'] = 'group.'.strtolower($user['rank']);
        $lpu['value'] = '1';
        $lpu['server'] = 'global';
        $lpu['world'] =  'global';
        $lpu['expiry'] = '0';
        $lpu['contexts']='{}';
        $luck_usr_perms->execute(array_values($lpu));
    }   
    $lastrank='default';
    $n = 10;//$ranksQ->rowCount() * 10;
    while($rank=$ranksQ->fetch(PDO::FETCH_ASSOC)){

        $lpg=array();
        $lpg['name'] = strtolower($rank['rank']);
        $luck_grp->execute(array_values($lpg));

        if(strlen($rank['prefix'])>0){
            $lp=array();
            $lp['name'] = strtolower($rank['rank']);
            $lp['permission'] = 'prefix.'.$n.'.'.$rank['prefix'];
            $lp['value'] = '1';
            $lp['server'] = 'global';
            $lp['world'] =  'global';
            $lp['expiry'] = '0';
            $lp['contexts']='{}';
            $luck_grp_perms->execute(array_values($lp));

            $lp=array();
            $lp['name'] = strtolower($rank['rank']);
            $lp['permission'] = 'weight.'.$n;
            $lp['value'] = '1';
            $lp['server'] = 'global';
            $lp['world'] =  'global';
            $lp['expiry'] = '0';
            $lp['contexts']='{}';
            $luck_grp_perms->execute(array_values($lp));
        }
        if(strlen($rank['suffix'])  > 0){
            $lp=array();
            $lp['name'] = strtolower($rank['rank']);
            $lp['permission'] = 'suffix.'.$n.'.'.$rank['suffix'];
            $lp['value'] = '1';
            $lp['server'] = 'global';
            $lp['world'] =  'global';
            $lp['expiry'] = '0';
            $lp['contexts']='{}';
            $luck_grp_perms->execute(array_values($lp));
        }

        if($rank['rank'] != 'default'){
            $lp=array();
            $lp['name'] = strtolower($rank['rank']);
            $lp['permission'] = 'group.'.strtolower($lastrank);
            $lp['value'] = '1';
            $lp['server'] = 'global';
            $lp['world'] =  'global';
            $lp['expiry'] = '0';
            $lp['contexts']='{}';
            $luck_grp_perms->execute(array_values($lp));
        }
        $lastrank = $rank['rank'];
        $n += 10;
    }
}

function perms_to_pex(){
    global $db;

    $pex_prep = insert_prep('pex_permissions',array('name','permission','type','world','value'));
    $pex_entity_prep = insert_prep('pex_permissions',array('name','type','nick'));
    $pex_inherit_prep = insert_prep('pex_permissions',array('child','parent','type'));
/*
Clear Tables
*/
    $db->query("DELETE FROM pex_permissions WHERE `type`=0 OR (`type`=1 AND `permission`='name')");
    $db->query("TRUNCATE TABLE pex_entity");
    $db->query("TRUNCATE TABLE pex_inheritance");

/*
Main Permissions
*/
    $permsQ = $db->query("SELECT * FROM mc_plugperms");//# Site defined permissions except invividual perms and system rows
    while($permi = $permsQ->fetch(PDO::FETCH_ASSOC)){
        if(strlen($permi['perm']) > 0 && substr($permi['perm'],-5) != '.NAME'){
            $pex=array();
            $pex['name'] = $permi['minrank'];
            $pex['permission'] = ($permi['bool'] == 'false' ? '-'  : '') . $permi['perm'];
            $pex['type'] = 0;
            $pex['world'] =  $permi['world'] != 'All' && $permi['world'] != null ? $permi['world'] : '';
            $pex['value'] = '';
            $pex_prep->execute(array_values($pex));
        }
    }
/*
Users & Ranks
*/
    $usersQ = $db->query("SELECT * FROM mc_players");// Site defined users
    $ranksQ = $db->query("SELECT * FROM mc_ranks");// Site defined ranks

    while($user = $usersQ->fetch(PDO::FETCH_ASSOC)){
        $pex_entity=array();
        $pex_entity['name'] = $user['uuid'];
        $pex_entity['type'] = 1;
        $pex_entity['nick'] = $user['player'];
        $pex_entity_prep->execute(array_values($pex_entity));

        $pex_inherit=array();
        $pex_inherit['child'] = $user['uuid'];
        $pex_inherit['parent'] = $user['rank'];
        $pex_inherit['type'] = 1;
        $pex_inherit_prep->execute(array_values($pex_inherit));

        $pex=array();
        $pex['name'] = $user['uuid'];
        $pex['type'] = 1;
        $pex['permission'] = 'name';
        $pex['value'] = $user['player'];
        $pex['world'] = '';
        $pex_prep->execute(array_values($pex));
    }     
    while($rank=$ranksQ->fetch(PDO::FETCH_ASSOC)){
        $pex_entity=array();
        $pex_entity['name'] = $rank['rank'];
        $pex_entity['type'] = 0;
        $pex_entity_prep->execute(array_values($pex_entity));

        if($rank['rank'] != 'default'){
            $pex_inherit=array();
            $pex_inherit['child'] = $rank['rank'];
            // TODO
            //$pex_inherit['parent'] = (string)get_object_or_404(Rank,order__exact=($rank['order']-1));
            $pex_inherit['type'] = 0;
            $pex_inherit_prep->execute(array_values($pex_inherit));
        }
        if(strlen($rank['prefix']) > 0){
            $pex=array();
            $pex['name'] = $rank['rank'];
            $pex['type'] = 0;
            $pex['permission'] = 'prefix';
            $pex['value'] = $rank['prefix'];
            $pex['world'] = '';
            $pex_prep->execute(array_values($pex));
        }
        if(strlen($rank['suffix'])  > 0){
            $pex=array();
            $pex['name'] = $rank['rank'];
            $pex['type'] = 0;
            $pex['permission'] = 'suffix';
            $pex['value'] = $rank['suffix'];
            $pex['world'] = '';
            $pex_prep->execute(array_values($pex));
        }
    }
}
?>
