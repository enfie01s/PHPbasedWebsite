<?php
if($loggedin){
$timeformat = 'Y-m-d H:i:s';
$todayObj = new DateTime('today');
$todayObj->setTime(00, 00, 00);

$yesterdayObj = clone($todayObj);
$tomorrowObj = clone($todayObj);
$dt_weekObj = clone($todayObj);
$dt_weekObj = clone($todayObj);
$dt_2weekagoObj = clone($todayObj);

$today = $todayObj->format($timeformat);

$yesterdayObj->sub(new DateInterval('P1D'));
$yesterday = $yesterdayObj->format($timeformat);

$tomorrowObj->add(new DateInterval('P1D'));
$tomorrow = $tomorrowObj->format($timeformat); 

$dt_weekObj->setTime(23, 59, 59);
$dt_weekObj->add(new DateInterval('P6D'));
$dt_week = $dt_weekObj->format($timeformat); 

$dt_2weekagoObj->sub(new DateInterval('P14D'));
$dt_2weekago = $dt_2weekagoObj->format($timeformat); 

$dt_1000Obj = new DateTime("1000-01-01 00:00:00");
$dt_1000 = $dt_1000Obj->format($timeformat); 

/* ****************** HOME ******************** */
    // TODO: display last downloaded episode - in popup? subquery.
    if(!isset($curpath[1])){
        try {
            tvdb();
        }
        catch(Exception $e){
            $data['errors'] = 'TVDB not available to update shows.';
        }
        /* *** LIST OF EPISODES DL TODAY *** */
        $dl_todayQ = $db->query('SELECT title,season,episode FROM shows_episodes WHERE downloaded BETWEEN "'.$today.'" AND "'.$tomorrow.'" ORDER BY title,season,episode');
        $data['dltoday']='';
        while($epi = $dl_todayQ->fetch(PDO::FETCH_ASSOC)){
            $data['dltoday'].='<br>'.$epi['title'].' '.episode_detail($epi['season'],$epi['episode']);}


        /* *** ERROR COLUMN *** */
        $epi_errorsQ = $db->query('SELECT *,t1.id as id FROM shows_episodes as t1 JOIN shows_series as t2 ON t1.series_id=t2.id WHERE t1.due >= "'.$dt_2weekago.'" AND t1.due < "'.$yesterday.'" AND t1.downloaded<="'.$dt_1000.'" AND t1.season>0 ORDER BY t1.due,t1.title');        
        ;
        $data['showserrors'] = $epi_errorsQ->fetchAll(PDO::FETCH_ASSOC);

        /* *** WEEKDAY COLUMNS *** */
        $weekday=clone($todayObj);
        for($x=0;$x<7;$x++){
            $data['weekdays'][] = $weekday->format($timeformat);
            $weekday->add(new DateInterval('P1D'));
        }
        $this_weekQ = $db->query('SELECT *,t1.id as id,DATE_ADD(`due`, INTERVAL 1 DAY) as due_plus FROM shows_episodes as t1 JOIN shows_series as t2 ON t1.series_id=t2.id WHERE t1.due BETWEEN "'.$yesterday.'" AND "'.$dt_week.'" AND t1.downloaded<="'.$dt_1000.'" AND t1.season>0 ORDER BY t1.due,t1.title');
        $data['thisweek'] = $this_weekQ->fetchAll(PDO::FETCH_ASSOC); 

        /* *** RSS LINKS *** */
        foreach($data['showserrors'] as $i => $v){
            $data['showserrors'][$i]['link']=rsslinks($v);}
        foreach($data['thisweek'] as $i => $v){
            $data['thisweek'][$i]['link']=rsslinks($v);}        

        $tpl->parse('home.html',$data);
    }

/* ****************** ONBREAK ******************** */
    elseif($curpath[1]=='onbreak'){

        $onbreakQ = $db->query('SELECT *,t2.title as title,t1.id as id,t1.season as season,t1.episode as episode,DATE_ADD(`due`, INTERVAL 1 DAY) as dueplus FROM shows_episodes as t1 JOIN shows_series as t2 ON t1.series_id=t2.id WHERE t2.id NOT IN (SELECT series_id FROM shows_episodes WHERE `due` BETWEEN "'.$yesterday.'" AND "'.$dt_week.'" AND season>0) AND t1.due >= "'.$dt_week.'" AND t1.downloaded<="'.$dt_1000.'" AND t1.season>0 AND t1.episode=(SELECT MIN(episode) FROM shows_episodes WHERE due >= "'.$dt_week.'" AND downloaded<="'.$dt_1000.'" AND season>0 AND series_id=t1.series_id AND season=t1.season) ORDER BY t1.due,t1.title');
        $data['onbreak']=$onbreakQ->fetchAll(PDO::FETCH_ASSOC);

        $inactiveQ = $db->query('SELECT *,MONTHNAME(firstaired) as month,DATE_ADD(`lastdue`, INTERVAL 1 DAY) as lastplus FROM shows_series as t1 WHERE id NOT IN (SELECT series_id FROM shows_episodes WHERE `due` > "'.$today.'" AND downloaded<="'.$dt_1000.'") ORDER BY MONTH(firstaired),t1.title');
        $data['inactive']=$inactiveQ->fetchAll(PDO::FETCH_ASSOC);

        $tpl->parse('onbreak.html',$data);
    }

/* ****************** MANAGETV ******************** */
    elseif($curpath[1]=='manage'){
        $post=array();
        $new = !isset($safepost['savecontinue'])&&!isset($safepost['save'])&&!isset($curpath[2])?1:0;

        /* ** GET SHOW ROW ** */
        if(!isset($safepost['savecontinue'])&&!isset($safepost['save'])&&isset($curpath[2])){
            $postQ = $db->prepare("SELECT * FROM shows_series WHERE `seotitle`=?");
            $postQ->execute(array($curpath[2]));
            $post = $postQ->fetch(PDO::FETCH_ASSOC);

            /* * DELETE * */
            if(isset($safepost['deleteshow'])&&strlen($safepost['id'])>0)
            {
                $dbQ = $db->prepare("DELETE FROM shows_series WHERE `id`=?");
                $dbQ->execute(array($safepost['id']));
                $dbQ = $db->prepare("DELETE FROM shows_episodes WHERE `series_id`=?");
                $dbQ->execute(array($safepost['id']));
                $fart=$_SERVER['DOCUMENT_ROOT']."/".$pagedir."/media/fart/".$post['tvdbid'].".jpg";
                $bart=$_SERVER['DOCUMENT_ROOT']."/".$pagedir."/media/bart/".$post['tvdbid'].".jpg";
                if(is_file($fart)) {unlink($fart);}
                if(is_file($bart)) {unlink($bart);}
                ?><script type='text/javascript'>window.location='/<?=$curpath[0]?>';</script><?php
            }
        }

        /* ** POST HANDLER ADD/EDIT ** */
        elseif(isset($safepost['savecontinue'])||isset($safepost['save'])){       
            /* Data */
            $safepost['overview']=$_POST['overview'];
            $post = $safepost;

            $data['errors'] = validate_form($form_managetv,$post,'shows_series');

            // Passed tests
            if(strlen($errors) < 1){
                /* update/add to db */
                $binds=array();
                $keys = array();
                foreach($form_managetv as $key => $vals){
                    $keys[]="`".$key."`";
                    $binds[]=isset($post[$key])?$post[$key]:'';
                }
                if(strlen($post['id'])>0){
                    $binds[]=$post['id'];
                    $dbQ = $db->prepare("UPDATE shows_series SET ".implode('=?,',$keys)."=? WHERE `id`=?");
                    $dbQ->execute($binds);
                    $backto=$curpath[0].'/'.$post['seotitle'];
                }
                else{
                    $dbQ = $db->prepare("INSERT INTO shows_series(".implode(',',$keys).",`lastlookup`) VALUES (?".str_repeat(',?',count($binds)-1).",'".$dt_1000."')");
                    $dbQ->execute($binds);
                    $post['id']=$db->lastInsertId();
                    $tvdbarg = isset($post['tvdbid'])?'':$post['title'];
                    tvdb($tvdbarg);
                    $backto=$curpath[0];
                    }
                $new=0;                    
                if(isset($post['save'])){// need to redirect to home if just add/save was clicked
                    ?><script type='text/javascript'>window.location='/<?=$backto?>';</script><?php
                }
            }else{
                $new = isset($post['id'])?0:1;
            }
            
        }

        /* ** SET UP THE PAGE ** */
        $data['form']=makeform($form_managetv,$post);        
        $data['post']=$post;
        $tpl->parse('manage.html',$data);
    }

/* ****************** MOVIES ******************** */
    elseif($curpath[1]=='movies'&&!isset($curpath[2])){

        $moviesQ = $db->query("SELECT *,(CASE WHEN got = 1 THEN 'faded' ELSE '' END) as fade FROM shows_movies WHERE `datestr` > NOW() ORDER BY `got`,`datestr`");
        $data['upcoming'] = $moviesQ->fetchAll(PDO::FETCH_ASSOC);

        $moviesQ = $db->query("SELECT *,(CASE WHEN got = 1 THEN 'faded' ELSE '' END) as fade FROM shows_movies WHERE `datestr` <= NOW() ORDER BY `got`,`datestr`");
        $data['released'] = $moviesQ->fetchAll(PDO::FETCH_ASSOC);

        $tpl->parse('movies.html',$data);
    }

/* ****************** MANAGEMOVIE ******************** */
    elseif($curpath[1]=='movies'&&$curpath[2]=='manage'){
        $new = !isset($safepost['savecontinue'])&&!isset($safepost['save'])&&!isset($curpath[3])?1:0;
        $post=array();
        if(!isset($safepost['savecontinue'])&&!isset($safepost['save'])&&isset($curpath[3])){
            $postQ = $db->prepare('SELECT * FROM shows_movies WHERE `seotitle`=?');
            $postQ->execute(array($curpath[3]));
            $post = $postQ->fetch(PDO::FETCH_ASSOC);
            $post['datestr'] = dateform($post['datestr'],'d/m/Y');

            /* * DELETE * */
            if(isset($safepost['deletemovie'])&&strlen($safepost['id'])>0)
            {
                $postQ = $db->prepare('SELECT banner FROM shows_movies WHERE `id`=?');
                $postQ->execute(array($safepost['id']));
                $post = $postQ->fetch(PDO::FETCH_ASSOC);
                $dbQ = $db->prepare("DELETE FROM shows_movies WHERE `id`=?");
                $dbQ->execute(array($safepost['id']));
                if(is_file($_SERVER['DOCUMENT_ROOT']."/".$pagedir."/media/mart/".str_replace('shows/','',$post['banner'])))
                {unlink($_SERVER['DOCUMENT_ROOT']."/".$pagedir."/media/mart/".str_replace('shows/','',$post['banner']));}
                 ?><script type='text/javascript'>window.location='/<?=$curpath[0]?>/movies';</script><?php
            }
        }

        /* ** POST HANDLER ** */
        elseif(isset($safepost['savecontinue'])||isset($safepost['save'])){

            /* * ADD/EDIT * */
            /* delete image if clear is ticked */
            if(isset($safepost['banner-clear'])&&strlen($safepost['id'])>0){
                unlink($_SERVER['DOCUMENT_ROOT']."/".$safepost['banner-clear']);
            }

            $dbits = explode("/",$safepost['datestr']);
            $dpath='mart/';//$dbits[2]."/".$dbits[1]."/".$dbits[0]."/";
            
            /* Image */
            $image='';
            $upload='none';
            if(isset($_FILES['banner'])){
                if($_FILES['banner']['error'] == 0){
                    $upload = uploadfile(
                        $_SERVER['DOCUMENT_ROOT']."/".$pagedir."/media/". $dpath,
                        $_FILES['banner']['name'],
                        $_FILES['banner']['tmp_name'],
                        $safepost['seotitle'],
                        '1024',
                        array('jpg','png','gif'));
                    if($upload == 'ok'){
                        $image = $dpath.$safepost['seotitle'].'.'.strtolower(pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION));
                        $safepost['banner'] = $image;
                    }else{$errors.='<br>'.$upload;}
                }
                else{$errors.='<br>'.$_FILES['banner']['error'];}
            }

            /* Data */
            $safepost['overview']=$_POST['overview'];
            $post = $safepost;

            $data['errors'] = validate_form($form_managemovie,$post,'shows_movies');

            // Passed tests
            if(strlen($data['errors'])<1){
                $binds=array();
                $keys = array();
                foreach($form_managemovie as $key => $vals){
                    $keys[]="`".$key."`";
                    if($vals['type']=='date'){$binds[] = $dbits[2]."-".$dbits[1]."-".$dbits[0]." 00:00:00";}
                    else{
                        $binds[]=isset($post[$key])?$post[$key]:'';
                    }
                }
                /* update/add to db */
                $imgup = strlen($image)>0?'`image`=?,':'';
                $imgadd = strlen($image)>0?'`image`,':'';
                if(strlen($post['id'])>0){
                    $binds[]=$post['id'];
                    $dbQ = $db->prepare("UPDATE shows_movies SET ".implode('=?,',$keys)."=? WHERE `id`=?");
                    $dbQ->execute($binds);
                }
                else{
                    $dbQ = $db->prepare("INSERT INTO shows_movies(".implode(',',$keys).") VALUES (?".str_repeat(',?',count($binds)-1).")");
                    $dbQ->execute($binds);
                    $post['id']=$db->lastInsertId();
                    }
                $backto=$curpath[0].'/movies';
                $new=0;                    
                if(isset($post['save'])){// need to redirect to home if just add/save was clicked
                    ?><script type='text/javascript'>window.location='/<?=$backto?>';</script><?php
                }
            }else{
                $new = isset($post['id'])?0:1;
            }
            
        }
        /* ** SET UP THE PAGE ** */   
        $data['form']=makeform($form_managemovie,$post);
        $data['post']=$post;
        $tpl->parse('managemovie.html',$data);
    }

/* ****************** VIEW SHOW ******************** */
    else{
        $episodesQ=$db->prepare("SELECT 
            t1.`title` as title,
            t2.`id` as eid,
            `season`,
            `episode`,
            `serid`,
            `overview`,
            `link`,
            `desc`,
            `seotitle`,
            `due`,
            DATE_ADD(`due`, INTERVAL 1 DAY) as due_plus,
            (CASE WHEN `downloaded` > '".$dt_1000."' THEN `downloaded` ELSE 'No' END) as downloaded,
            `tvdbid`,
            `imdb`,
            `rssfeedid`,
            (CASE WHEN `due` > NOW() THEN 'faded' ELSE '' END) as future
             FROM shows_series as t1 LEFT JOIN shows_episodes as t2 ON t1.id=t2.series_id WHERE `seotitle`=? AND (CASE WHEN t2.series_id THEN `season` != 0 ELSE 1 END) ORDER BY season DESC,episode DESC");
        $episodesQ->execute(array($curpath[1]));
        $data['seasons']=array();
        while($episodes=$episodesQ->fetch(PDO::FETCH_ASSOC)){
            if(!isset($data['seasons'][$episodes['season']])){$data['seasons'][$episodes['season']]=array();}
            $data['seasons'][$episodes['season']][$episodes['episode']]=$episodes;
            $data['show'] = $episodes;       
        }
        $tpl->parse('show_detail.html',$data);
    }
    if(isset($errors)){$data['errors']=$errors;}
}
else{
    $data['errors']='Please log in to access this page.';//error alerts
}
?>