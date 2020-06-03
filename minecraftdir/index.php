<?php
/* ********************** BASE ********************** */    
    $headimgs = glob($pagedir.'/static/img/headers/*.jpg');
    $rand = rand(0,count($headimgs)-1);    
    $data['headerbg'] = $headimgs[$rand];
    //server status
    //http://aristia.net/minecraftdir/static/lib/query1/view_serverping.php
    use xPaw\MinecraftPing;
    use xPaw\MinecraftPingException;

    define( 'MQ_SERVER_ADDR', 'mc.aristia.net' );
    define( 'MQ_SERVER_PORT', 25565 );
    define( 'MQ_TIMEOUT', 1 );

    require $pagedir.'/static/lib/query1/src/MinecraftPing.php';
    require $pagedir.'/static/lib/query1/src/MinecraftPingException.php';

    $Info = false;
    $Query = null;

    try
    {
        $Query = new MinecraftPing( MQ_SERVER_ADDR, MQ_SERVER_PORT, MQ_TIMEOUT, 'false');

        $Info = $Query->Query( );        
    }
    catch( MinecraftPingException $e )
    {
        $Exception = $e;
    }

    if( $Query !== null )
    {
        $Query->Close( );
    }
    if( $Info !== false && !isset( $Exception )){
        $data['status']['online'] = $Info['players']['online'];
        $data['status']['max'] = $Info['players']['max'];
        //echo $Info['description']['text'];
        //echo $Info['version']['name'];
        $data['status']['favicon']=str_replace( "\n", "", $Info['favicon'] );
    }
    else{
        $data['status']='Offline';
    }

    
/* ********************** HOME ********************** */
if(!isset($curpath[1])){
    $tpl->parse('home.html',$data);
}

/* ********************** PLAYER ********************** */
elseif($curpath[1]=='players'&&isset($curpath[2])){
    $data['player'] = $curpath[2];
    $tpl->parse('player.html',$data);
}

/* ********************** PLAYERS ********************** */
elseif($curpath[1]=='players'){
    //,COUNT(session_start) as sesscount,MAX(mob_kills) as maxmob,SUM(mob_kills) as summob,MAX(deaths) as maxdeath,SUM(deaths) as sumdeath,MAX(afk_time/1000) as maxafk,SUM(afk_time/1000) as sumafk
    $playersQ = $db->query("
        SELECT *,t2.`rank` as rankname 
        FROM mc_players as t1 JOIN mc_ranks as t2 ON t1.`rank`=t2.`id` 
        LEFT JOIN (
            SELECT t3a.`uuid`, FROM_UNIXTIME(registered/1000) AS registered, FROM_UNIXTIME(MAX(session_start)/1000) AS sess_start, FROM_UNIXTIME(MAX(session_end)/1000) AS sess_end,(MAX(session_end)-MAX(session_start))/1000 as sesslen,MAX(mob_kills) as maxmob,SUM(mob_kills) as alltimemob,AVG(mob_kills) as avgmob,MAX(afk_time)/1000 as maxafk,SUM(afk_time) as alltimeafk,AVG(afk_time) as avgafk,MAX(deaths) as maxdeath,SUM(deaths) as alltimedeath,AVG(deaths) as avgdeath,(SUM(session_end-session_start)/COUNT(session_start))/1000 as avgsess 
            FROM plan_users as t3a 
            LEFT JOIN plan_sessions as t3b ON t3a.`uuid`=t3b.`uuid`
            GROUP BY `uuid`
        ) as t3 ON t1.`uuid`=t3.`uuid` 
        ORDER BY t3.`sess_end` DESC");
    $data['players'] = $playersQ->fetchAll(PDO::FETCH_ASSOC);
    //print_r(glob('minecraftdir/*'));#nginx has a location set for path /userdata/.
    /*
    foreach($data['players'] as $idx => $player){
        $dt = new DateTime();
        $data['players'][$idx]['lastseen']=$dt->format('Y-m-d');
        //$file=dirname($_SERVER['DOCUMENT_ROOT']).'/nucleusdata/' . substr($player['uuid'],0,2) . '/' . $player['uuid'] . '.json';
        //$file='/userdata/' . substr($player['uuid'],0,2) . '/' . $player['uuid'] . '.json';
        
        $file='/userdata/' . substr($player['uuid'],0,2) . '/' . $player['uuid'] . '.json';
        if(is_file($file)){
            echo $file;
            $nucleusfile = json_decode(file_get_contents($file));
            $dt = new DateTime();
            $dt->setTimestamp(substr($nucleusfile->lastLogout,0,-3));//-3 to remove millliseconds
            $data['players'][$idx]['lastseen']=$dt->format('Y-m-d h:i:s');
        }else{
            $data['players'][$idx]['lastseen']=1;
        }
        
    }
    */
    if(isset($Info['players']['sample'])){
        foreach($Info['players']['sample'] as $playr){
            $data['onlineplayers'][]=$playr['id'];
        }
    }
    //$Info['players']['sample'];//array(0=>array('id'=>'','name'=>''))
    //echo $Info['modinfo']['modList'];//array(0=>array('modid'=>'','version'=>''))
    $tpl->parse('players.html',$data);
}

/* ********************** GALLERY ********************** */
/*
def gallery(request,player=''):
    #gallerypath = 'minecraft/img/gallery/' + gdir
    media_dir = 'minecraft/gallery'  # get current directory
    images = {}
    if len(player) > 0:
        media_dir += '/' + player + '/'
    mdir = media_dir
    if not os.path.exists(settings.MEDIA_ROOT + '/' + media_dir):
        os.makedirs(settings.MEDIA_ROOT + '/' + media_dir)
    media_dir = os.path.normpath(settings.MEDIA_ROOT + '/' + media_dir)
    for im in os.listdir(media_dir):
        pathf = os.path.join(media_dir, im)
        if os.path.isfile(pathf):
            displayname = os.path.splitext(im)[0]
            images[displayname] = [im,displayname.replace('-',' ')]
    #print(images)
    return render(request,'minecraft/gallery.html',{'images':images,'mdir':mdir})
 */

/* ********************** COMMANDS ********************** */
elseif($curpath[1]=='commands'){
    $data['form'] = makeform($commandsform,$safeget);
    $extra =  isset($safeget['pl'])&&strlen($safeget['pl'])>0?'rank':'pl';
    $commsS = "SELECT *, ".$extra." as extra FROM mc_plugperms as t1 JOIN mc_ranks as t2 ON t1.`minrank`=t2.`id` WHERE `pl` IN (".$availplugs.",'default') AND `comm`!='Ignore' AND `comm` IS NOT NULL AND `comm` != 'None'";
    $binds=array();
    if(isset($safeget['minrank']) && strlen($safeget['minrank']) > 0){
        $binds['minrank'] = $safeget['minrank'];
        $commsS .= " AND `minrank`=?";
    }
    elseif(!isset($safeget['pl']) || strlen($safeget['pl']) == 0){
        $binds['minrank'] = 10;
        $commsS .= " AND `minrank`=?";
    }

    if(isset($safeget['pl']) && strlen($safeget['pl']) > 0){
        $binds['pl'] = $safeget['pl'];
        $commsS .= " AND `pl`=?";
    }
    $commsQ = $db->prepare($commsS." GROUP BY `comm` ORDER BY `pl`,`minrank` DESC");
    $commsQ->execute(array_values($binds));
    $data['commands'] = $commsQ->fetchAll(PDO::FETCH_ASSOC);

    $data['plugins'] = array();
    foreach($plugins as $plug){
        $data['plugins'][]=explode(" ",$plug);
    }
    $tpl->parse('commands.html',$data);
}

/* ********************** INFO ********************** */
elseif($curpath[1]=='info'){
    $data['kits'] = array();
    foreach($kits as $kit=>$details){
        $data['kits'][$kit] = array(
        'cost'=>$details[0] > 0 ? (string)$details[0] . ' Notes' : 'Free',
        'time'=>($details[1] / 60),
        'rank'=>$details[3],
        'contents'=>array()
        );
        
        foreach($details[2] as $detail => $extra){
            $item_qty = explode("-",$detail);
            $item_id = str_ireplace(':','_',$item_qty[0]);
            $item_name = array_key_exists('name',$extra) ? prettycolours($extra['name']) : $itemdata[$item_id][0];
            $iinpos = array_key_exists($item_id,$itempos);
            $posL = $iinpos ? $itempos[$item_id][0] : '0';
            $posT = $iinpos ? $itempos[$item_id][1] : '0';
            $itype = 'itemimg';
            if(substr($item_qty[0],0,4)=='373_'){
                $itype = 'itempotion';
            }
            $item = array(
                'itemid'=>$item_id,
                'qty'=>$item_qty[1],
                'title'=>$item_name,
                'horiz'=>$posL,
                'vert'=>$posT,
                'class'=>$itype
                );
            $moreinfo = '';
            if(count($extra) > 0){
                $edetail = '';
                $lore = '';
                foreach($extra as $atr=>$val){
                    if($atr != 'name'){
                        if($atr == 'lore'){
                            $lore = $val;
                        }
                        else{
                            $edetail .= (count($edetail)>0 ? ', ' : '') . $atr . ' = ' . (string)$val;
                        }
                    }
                }
                if(strlen($lore) > 0){
                    $moreinfo = $lore;
                }
                if(strlen($edetail) > 0){
                    $moreinfo .= ' (' . $edetail . ')';
                }
            }

            if (strlen($moreinfo) > 0){
                $item['moreinfo']=$moreinfo;
            }
            $data['kits'][$kit]['contents'][]=$item;
        }
    }
    $data['grids'] = array();
    foreach($itemdata as $i => $a){
        $thegrid = grids($i);
        if(isset($thegrid)){
            $data['grids'][$i] = $thegrid;
        }
    }
    foreach($msettings as $k => $v)
    {
        $data[$k] = $v;
    }
    $data['nukits'] = kits_to_ess();

    $tpl->parse('info.html',$data);
}
?>
