<?php

function episode_detail($s,$e){
    return 'S:'.$s.' E:'.$e;
}
function download_link($link,$id){
    if(strlen($link)>0){
        return '<a data-dllink="'.$link.'" id="'.$id.'" class="dl-overlay dl-link"><i class="fa fa-download" aria-hidden="true"></i></a>';
    }
}


/* XML Parsing */
function getFeed($feedid){
    $feeds = get_data("http://showrss.info/show/".$feedid.".rss");
    $items = array();
    if($feeds==503){$errors='503 while getting RSS';}
    else{
        $rss = simplexml_load_string($feeds);

        foreach($rss->channel->item as $entry) {
            $image = '';
            $image = 'N/A';
            $description = 'N/A';
            foreach ($entry->children('media', true) as $k => $v) {
                $attributes = $v->attributes();

                if ($k == 'content') {
                    if (property_exists($attributes, 'url')) {
                        $image = $attributes->url;
                    }
                }
                if ($k == 'description') {
                    $description = $v;
                }
            }

            $items[] = array(
                'link' => (string)$entry->link,
                'title' => (string)$entry->title,
                'description' => (string)$description,
                'pubDate' => (string)$entry->pubDate,
            );
        }
    }
    return $items;
}

function rsslinks($episode){
    global $db,$errors;
    $xml=array();
    try{
    $xml = @getFeed($episode['rssfeedid']);
    }
    catch(Exception $e){$errors.="<br>Error getting RSS Feed for ".$episode['title'];}
    $upQ = $db->prepare("UPDATE shows_episodes SET `link`=?, `rssdate`=? WHERE `series_id`=? AND `season`=? AND `episode`=?");
    $epidone=0;
    $epihd=0;
    $showlink = '';
    foreach($xml as $idx => $data){
        $title = $data['title'];
        $link = $data['link'];
        preg_match_all( "/(special)/i",   $link, $isspecial );
        if(count($isspecial[0])<1){
            preg_match_all( "/S([\d]{2})E([\d]{2})/i",   $link, $se_info );
            if($se_info[1][0]==$episode['season'] && $se_info[2][0] == $episode['episode']){
                preg_match_all( "/(720p|1080p)/",   $link, $ishd );
                $pub_date = new DateTime($data['pubDate']);
                $pubDate = $pub_date->format('Y-m-d h:i:s');

                $hd = isset($ishd) && isset($ishd[1][0]) ? $ishd[1][0] : 0;
                $binds=array(
                    $link,
                    $pubDate,
                    $episode['series_id'],
                    $episode['season'],
                    $episode['episode']);

                $thisepi=$se_info[1][0].'x'.$se_info[2][0];

                if($epidone!=$thisepi||$hd=='1080p')//not already done or if 1080p
                    {$epihd=$hd=='1080p'?2:0;$upQ->execute($binds);$showlink = $link;}

                elseif(($epidone!=$thisepi||$epihd==0)&&$hd=='720p')//not already done or is sd, and if 720p
                    {$epihd=1;$upQ->execute($binds);$showlink = $link;}

                elseif($epidone!=$thisepi)//not already done
                    {$epihd=0;$upQ->execute($binds);$showlink = $link;}

                $epidone = $thisepi;
            }
        }
    }
    return $showlink;
}

/* gets the data from a URL */
function get_data($url) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);

    // Check HTTP status code
    if (!curl_errno($ch)) {
      switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
        case 200:  # OK
          break;
        default:
          return $http_code;
      }
    }

    curl_close($ch);
    return $data;
}

/* TVDB */
//https://api.thetvdb.com/swagger#/
function tvdb($showname=''){
    //echo $showname;
    global $pagedir,$db,$tvdbtoken;
    //TheTVDB auth stuff
    $userinfo = array (
        'username' => 'Aristia',
        'userkey' => 'CAA38121E7C3FB46',
        'apikey' => 'FD3D797D13D29667'
    );
    $tvdbtoken = tvdb_post('https://api.thetvdb.com/login', $userinfo);
    //print_r($tvdbtoken);
    //$tvdbtoken = tvdb_api('https://api.thetvdb.com/refresh_token');

    $today = new DateTime('today');
    $dt00 = clone($today);
    $dt10 = clone($today);
    $dt20 = clone($today);

    $dt00->sub(new DateInterval('P2D'));
    $dt0=$dt00->format('Y-m-d h:i:s');
    $dt10->sub(new DateInterval('P30D'));
    $dt1=$dt10->format('Y-m-d h:i:s');
    $dt20->sub(new DateInterval('P500D'));
    $dt2=$dt20->format('Y-m-d h:i:s');
    $dt_1000='1000-01-01 00:00:00';

    $where = strlen($showname)>0?"`title`=?":"`lastlookup` < ?";
    $binds = strlen($showname)>0?array($showname):array($dt0);
    $series = $db->prepare("SELECT * FROM shows_series WHERE ".$where);
    $series->execute($binds);
    while($show=$series->fetch(PDO::FETCH_ASSOC)){
        ?><script>console.log('<?php echo $where." - ".$show["title"];?>')</script><?php
        if(strlen($show['tvdbid'])>0&&$show['tvdbid']!=0){
            $result = tvdb_api('https://api.thetvdb.com/series/'.$show['tvdbid']);
            $tvdbshow = array_key_exists('data',$result)?$result['data']:array();
        }else{
            $result = tvdb_api('https://api.thetvdb.com/search/series?name='.stripslashes($show['title']));
            $tvdbshow = array_key_exists('data',$result)?$result['data'][0]:array();
        }
        if(count($tvdbshow)>0){
            $seasonsr = tvdb_api("https://api.thetvdb.com/series/".$tvdbshow['id']."/episodes/summary");
            $asr=$seasonsr['data']['airedSeasons'];
            sort($asr);
            $seasons = strlen($showname) > 0||$show['lastlookup'] == $dt_1000 ? array_slice($asr,-2) : array_slice($asr,-1);//last 2 seasons if new
            $lastDue = $tvdbshow['firstAired'].' 00:00:00';
            $lastEpi = '1x1';
            foreach($seasons as $tvdbseason){
                $episodesr=tvdb_api("https://api.thetvdb.com/series/".$tvdbshow['id']."/episodes/query?airedSeason=".$tvdbseason);
                $episodes = $episodesr['data'];
                foreach($episodes as $tvdbepi){
                    //delete old episodes <= $dt2
                    $airedDT='';
                    if(strlen($tvdbepi['firstAired'])>0){
                        $airedDT=$tvdbepi['firstAired'].' 00:00:00';
                    }
                    $airedDT1=new DateTime($airedDT);
                    //Newly added show otherwise no further back than 2 days
                    if((strlen($showname) > 0||$show['lastlookup'] == $dt_1000)||(strlen($airedDT) > 0 && $airedDT1 > $dt00)){
                        //check for dupe here
                        $dupeQ=$db->prepare("SELECT * FROM shows_episodes WHERE `season`=? AND `episode`=? AND `serid`=?");
                        $dupeQ->execute(array($tvdbepi['airedSeason'],$tvdbepi['airedEpisodeNumber'],$tvdbshow['id']));
                        $binds=array(
                            $today->format('Y-m-d 00:00:00'),
                            $airedDT,
                            $tvdbepi['overview'],
                            $tvdbshow['id'],
                            $tvdbepi['id'],
                            $tvdbepi['airedSeasonID'],
                            $show['id']
                        );
                        $adorup='';
                        if($dupeQ->rowCount()>0){
                            $dupe=$dupeQ->fetch(PDO::FETCH_ASSOC);
                            if($dupe['downloaded']==$dt_1000){//ignore downloaded episodes
                                $adorup='Updating ';
                                $binds[]=$dupe['rssdate']!=$dt_1000?$dupe['rssdate']:$dt_1000;
                                $binds[]=$dupe['downloaded']!=$dt_1000?$dupe['downloaded']:$dt_1000;
                                $binds[]=$dupe['id'];
                                $modDb = $db->prepare("UPDATE shows_episodes SET 
                                `date_added`=?,
                                `due`=?,
                                `desc`=?,
                                `serid`=?,
                                `epiid`=?,
                                `seaid`=?,
                                `series_id`=?,                            
                                `rssdate`=?,
                                `downloaded`=?
                                WHERE `id`=?");
                            }
                        }else{
                            $adorup='Adding ';
                            $binds[]=$dt_1000;
                            $binds[]=$dt_1000;
                            $binds[]=$tvdbshow['seriesName'];
                            $binds[]=$tvdbepi['airedSeason'];
                            $binds[]=$tvdbepi['airedEpisodeNumber'];
                            $modDb = $db->prepare("INSERT INTO shows_episodes(
                            `date_added`,
                            `due`,
                            `desc`,
                            `serid`,
                            `epiid`,
                            `seaid`,
                            `series_id`,
                            `rssdate`,
                            `downloaded`,
                            `title`,
                            `season`,
                            `episode`
                            ) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
                        }                    
                        if(strlen($adorup)>0){
                            $modDb->execute($binds);//update/add episodes to db here
                            $adorup.=$show['title'].' S'.$tvdbepi['airedSeason'].'E'.$tvdbepi['airedEpisodeNumber'].', date '.$airedDT;
                            ?><script>console.log('<?php echo $adorup;?>')</script><?php
                        }
                    }
                    if(strlen($airedDT)>0){
                        $lastDue=$airedDT;
                        $lastEpi=$tvdbepi['airedSeason'].'x'.$tvdbepi['airedEpisodeNumber'];
                    }
                }
            } 
            $binds = array(
            $tvdbshow['id'],
            $tvdbshow['overview'],
            $tvdbshow['firstAired'].' 00:00:00',
            $lastDue,
            $lastEpi,
            isset($tvdbshow['imdbId'])?$tvdbshow['imdbId']:'',
            $show['id']
            );  
            //update shows_series here
            $modShow = $db->prepare("UPDATE shows_series SET 
            `lastlookup`=NOW(),
            `tvdbid`=?,
            `overview`=?,
            `firstaired`=?,
            `lastdue`=?,
            `lastepi`=?,
            `imdb`=? WHERE id=?");
            $modShow->execute($binds);
            /* GET IMAGES */
            $curFart=$pagedir."/media/fart/".$tvdbshow['id'].".jpg";
            $curBart=$pagedir."/media/bart/".$tvdbshow['id'].".jpg";
            if(!is_file($curFart)||!is_file($curBart)){
                $fanartQ=tvdb_api("https://api.thetvdb.com/series/".$tvdbshow['id']."/images/query?keyType=fanart&resolution=1920x1080");
                $fanart = $fanartQ['data'][0];

                if(!is_file($curFart)&&strlen($fanart['thumbnail'])>0){
                    file_put_contents($curFart, file_get_contents('http://www.thetvdb.com/banners/'.$fanart['thumbnail']));
                    //get_img('http://www.thetvdb.com/banners/'.$fanart['thumbnail'],$curFart);
                    chmod($curFart,0777);
                }
                if(!is_file($curBart)&&strlen($fanart['fileName'])>0){
                    file_put_contents($curBart, file_get_contents('http://www.thetvdb.com/banners/'.$fanart['fileName']));
                    //get_img('http://www.thetvdb.com/banners/'.$fanart['fileName'],$curBart);
                    chmod($curBart,0777);
                }
            }
        }
        else{
            $binds = array(
            $show['id']
            ); 
            //update shows_series here
            $modShow = $db->prepare("UPDATE shows_series SET 
            `lastlookup`=NOW()
             WHERE id=?");
            $modShow->execute($binds);
            ?><script>console.log('<?php echo "Setting last lookup for ".$show["title"];?>')</script><?php
        }

    }
}

function tvdb_post ($url, $userinfo) {
    $ch = curl_init($url);
    $payload = json_encode($userinfo);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    $return = json_decode($result, true);

    return $return; 
}

function tvdb_api ($url) {
    global $tvdbtoken;
    $authorization = "Authorization: Bearer ".$tvdbtoken['token'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $json = curl_exec($ch);
    curl_close($ch);
    $array = json_decode($json, true);

    return $array;
}

?>