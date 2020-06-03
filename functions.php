<?php
function datecompare($date1,$date2,$difflevel=1)
{
    $dt1 = new DateTime($date1);
    $dt2 = new DateTime($date2);
    $diff= date_diff($dt1, $dt2);
    $format = '%a';
    if($difflevel>1){
        $return = '';
        $count = 0;
        $days=$diff->format('%a');
        if($diff->format('%y')>0)
            {$y=$diff->format('%y');$return.=$y." Year".plural($y)." ";$count++;}
        if($diff->format('%m')>0)
            {$m=$diff->format('%m');$return.=$m." Month".plural($m)." ";$count++;}
        if($diff->format('%d')>0&&$count<$difflevel)
            {$d=$diff->format('%d');$return.=$d." Day".plural($d)." ";$count++;}
        if($diff->format('%h')>0&&$count<$difflevel)
            {$h=$diff->format('%h');$return.=$h." Hour".plural($h)." ";$count++;}
        if($diff->format('%i')>0&&$count<$difflevel)
            {$i=$diff->format('%i');$return.=$i." Minute".plural($i)." ";$count++;}
        if($diff->format('%s')>0&&$count<$difflevel)
            {$s=$diff->format('%s');$return.=$s." Second".plural($s)." ";$count++;}
        return $return;
    }
    else
    {
        return $diff->format($format);
    }
}
function plural($num){
    return $num == 1?"":"s";
}

function dateform($datetime,$format)
{
    $datetime = new DateTime($datetime);
    return $datetime->format($format);
}

function mkdirRe($dir)
{
    $dirs=explode("/",$dir);
    $dirsofar="";
    foreach($dirs as $d => $tdir)
    {
        if(strlen($dirsofar)>0&&$dirsofar!='../'&&!is_dir($dirsofar)){mkdir($dirsofar);}
        $dirsofar.=$tdir."/";
    }
}
/**
 * Handle an uploaded file.
 * @param  string  $target_path Where to put the file.
 * @param  string  $file_name   Uploaded file's name (part of the php $_FILES array)
 * @param  string  $tmp_name    Uploaded file's temp name (part of the php $_FILES array)
 * @param  string  $newname     An optional new name to give the uploaded file.
 * @param  integer  $maxsize     Maximum allowed file size.
 * @param  array  $allowed     Allowed file extensions.
 * @param  integer $maxw        Maximum width (scale image if over this width)
 * @param  integer $maxh        Maximum height (scale image if over this height)
 * @return string               Returns a status message ('ok' or an error message)
 */
function uploadfile($target_path,$file_name,$tmp_name,$newname,$maxsize,$allowed,$maxw=0,$maxh=0)
{
    $upload_ok = 1;
    $size1 = number_format(filesize($tmp_name) / 1024,2);
    $filename1 = basename($file_name);
    $extension1 = strtolower(pathinfo($filename1, PATHINFO_EXTENSION));
    $error="";
    if(count($allowed)>0&&!in_array($extension1, $allowed)){$error="Error, file extension: $extension1 of file: $file_name not allowed, please try again.";}
    else if($size1 > $maxsize){$error="Error, file too big (".($size1-$maxsize)."k over the limit) please try again.";}
    else if(strlen($filename1) < 1){$error="Error, please choose a file to upload";}

    $upload_ok=strlen($error)>0?0:1;
    if ($upload_ok == 1)
    {
        $target_path1 = $target_path . (strlen($newname)>1?$newname . "." . $extension1:$filename1);
        if ($file_name)
        {
            if(!is_dir($target_path))
            {
                mkdirRe($target_path);
            }
            if(move_uploaded_file($tmp_name, $target_path1))
            {
                if(($maxw>0||$maxh>0)&&$extension1=="jpg")
                {scaleimg($target_path1,$maxw,$maxh,1);}
                chmod($target_path1, 0644);
                return 'ok';
            }
            else
            {
                return "There was an error moving the file, please try again!";
            }
        }
        else
        {
            return "There was an error uploading the file, please try again!";
        }
    }
    else
    {
        return $error;
    }
}

/*
    Truncates text.

    Cuts a string to the length of $length and replaces the last characters
    with the ending if the text is longer than length.

    ### Options:
    - `ending` Will be used as Ending and appended to the trimmed string
    - `exact` If false, $text will not be cut mid-word
    - `html` If true, HTML tags would be handled correctly

    :param $text: String to truncate.
    :integer $length: Length of returned string, including ellipsis.
    :array $options: An array of html attributes and options.
    :returns: Trimmed string
*/
class CustomFilter{
    /* {{ {var}|truncatehtml:{arg} }} */
    function filter_truncatehtml($var, $arg = 100) {
        $default = array(
            'ending' => '...', 'exact' => true, 'html' => true
        );
        $options = $default;
        extract($options);

        if ($html) {
            if (mb_strlen(preg_replace('/<.*?>/', '', $var)) <= $arg) {
                return $var;
            }
            $totalLength = mb_strlen(strip_tags($ending));
            $openTags = array();
            $truncate = '';

            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $var, $tags, PREG_SET_ORDER);
            foreach ($tags as $tag) {
                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                    if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                        array_unshift($openTags, $tag[2]);
                    } else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                        $pos = array_search($closeTag[1], $openTags);
                        if ($pos !== false) {
                            array_splice($openTags, $pos, 1);
                        }
                    }
                }
                $truncate .= $tag[1];

                $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
                if ($contentLength + $totalLength > $arg) {
                    $left = $arg - $totalLength;
                    $entitiesLength = 0;
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entitiesLength <= $left) {
                                $left--;
                                $entitiesLength += mb_strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }

                    $truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
                    break;
                } else {
                    $truncate .= $tag[3];
                    $totalLength += $contentLength;
                }
                if ($totalLength >= $arg) {
                    break;
                }
            }
        } else {
            if (mb_strlen($var) <= $arg) {
                return $var;
            } else {
                $truncate = mb_substr($var, 0, $arg - mb_strlen($ending));
            }
        }
        if (!$exact) {
            $spacepos = mb_strrpos($truncate, ' ');
            if (isset($spacepos)) {
                if ($html) {
                    $bits = mb_substr($truncate, $spacepos);
                    preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                    if (!empty($droppedTags)) {
                        foreach ($droppedTags as $closingTag) {
                            if (!in_array($closingTag[1], $openTags)) {
                                array_unshift($openTags, $closingTag[1]);
                            }
                        }
                    }
                }
                $truncate = mb_substr($truncate, 0, $spacepos);
            }
        }
        $truncate .= $ending;

        if ($html) {
            foreach ($openTags as $tag) {
                $truncate .= '</'.$tag.'>';
            }
        }

        return $truncate;
        }

    /* {{ {var}|ireplace:{arg}|{arg} }} */
    function filter_ireplace($var,$match='',$replace='')
    {
        return str_ireplace($match,$replace,$var);
    }
    /* minecraft bits */
    function filter_pretty($string){
        global $colours;
        $pref = '<span>' . $string . '</span>';
        foreach($colours as $k=>$v){
            $pref = str_ireplace('&'.$k, '</span><span style=\'color:'. $v .'\'>',$pref);
        }
        return $pref;
    }
    /* shows bits */
    function filter_audience($audience){
        global $loggedinuser;

        if(strlen($audience)>0){
            if($loggedinuser == 'm3nf'){
            return "<span class='" . strtolower($audience) . "'>" .substr($audience,0,1) . "</span> ";}
            else{
            return "<i class='fa fa-user-circle " . strtolower($audience) . "' aria-hidden='true'></i> ";}

        }
        return '';
    }
    function filter_evenodd($var){
        return $var % 2 == 0 ? 'even' : 'odd';
    }
    function filter_stripslashes($var){
        return stripslashes($var);
    }
}

/* *********** AUTH ************ */
if(!function_exists('password_verify')||!function_exists('password_hash'))
{
    function password_verify($matchtry , $matchto){
        if(crypt($matchtry,$matchto)==$matchto){return true;}else{return false;}
    }
    function password_hash($h , $t){
        global $condiment;
        return crypt($h,$condiment);
    }
}
if(isset($safeget['makepass'])){echo password_hash($_GET['makepass'], PASSWORD_BCRYPT);}

if(isset($safepost['password'])){
    /* handle posted form */
    //search for user and get pass
    $uQ = $db->prepare('SELECT id,username,password,loggedin FROM auth WHERE `username`=?');
    $uQ->execute(array($safepost['username']));
    $u = $uQ->fetch(PDO::FETCH_ASSOC);
    if(password_verify( $_POST['password'] , $u['password'])){
        $cookiehash = strlen($u['loggedin'])>0 ? $u['loggedin'] : password_hash($_POST['password'], PASSWORD_BCRYPT);//make hash

        setcookie("loggedhash", $cookiehash, time()+31536000, $cookie_path, $cookie_domain, 0);//lasts a year
        if(strlen($u['loggedin'])<1){
            $db->query("UPDATE auth SET `loggedin`='$cookiehash' WHERE `id`={$u['id']}");//update db
        }
        $_SESSION['loggedin']=$u['username'];
    }elseif(isset($_COOKIE['loggedhash'])){setcookie('loggedhash','',time()-2000);}
    /* /handle posted form */
}

//if not logged in and cookie found, check for user with cookiepass set loggedin session
if(!isset($_SESSION['loggedin']) && isset($_COOKIE['loggedhash']))
{
    $uQ = $db->prepare('SELECT id,username FROM auth WHERE `loggedin`=?');
    $uQ->execute(array($_COOKIE['loggedhash']));
    $u = $uQ->fetch(PDO::FETCH_ASSOC);
    if($uQ->rowCount()>0){$_SESSION['loggedin']=$u['username'];}
    else{unset($_SESSION['loggedin']);setcookie('loggedhash','',time()-2000);}
}
$loggedin = isset($_SESSION['loggedin']) ? 1 : 0;
$loggedinuser = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : '';
/* *********** FORMS ************ */

function makeform($elements=array(),$values=array()){
    global $pagedir;
    $returnform='';
    $find = array('[type]','[name]','[class]','[faclass]','[placeholder]','[required]','[value]');
    foreach ($elements as $name => $data){
        $class = array_key_exists('class',$data)?$data['class']:'';
        if($data['type']!='select'&&isset($data['options'])&&is_array($data['options'])){
            $returnform.='<h4><i class="fa fa-[faclass] fa-fw" aria-hidden="true"></i> [placeholder]</h4><div class="form-inline '.$class.'">';
        }
        else{
        $returnform.='<div class="input-group '.$class.'"><label for="id_[name]" class="input-group-addon" title="[placeholder]"><i class="fa fa-[faclass] fa-fw" aria-hidden="true"></i></label>';
}
        switch ($data['type']){
            case 'textarea':
                $returnform.="<textarea name='[name]' id='id_[name]' class='[class]' placeholder='[placeholder]' [required]>[value]</textarea>";
                break;
            case 'select':
                $returnform.="<select name='[name]' id='id_[name]' class='[class]' [required]>";
                $returnform.="<option value=''>[placeholder]</option>";
                if(isset($data['options'])){
                    foreach($data['options'] as $idx => $option){
                        $optionval = isset($data['options_vals'])?$data['options_vals'][$idx]:$option;
                        $returnform.="<option value='".$option."' ".(isset($values[$name])&&$values[$name]==$option?"selected='selected'":"").">".ucwords($optionval)."</option>";
                    }
                }
                $returnform.="</select>";
                break;
            default:
                if($data['type']=='file'){
                    $values[$name] = isset($values[$name])?str_replace(array('shows/','blog/'),array('',''),$values[$name]):'';
                    $returnform.='<span class="form-control" style="display:inline">';
                    if(isset($values[$name])&&is_file($pagedir.'/media/'.$values[$name]))
                    {
                        $returnform.='Currently: <a href="'.$pagedir.'/media/[value]" target="_blank">'.$pagedir.'/media/[value]</a>
                        <input type="checkbox" name="[name]-clear" id="[name]-clear_id" value="[value]">
                        <input type="hidden" name="[name]" id="id_[name]" value=\"[value]\">
                        <label for="[name]-clear_id">Clear</label><br>
                        Change:';
                    }
                }
                if(isset($data['options'])&&is_array($data['options'])){
                    foreach($data['options'] as $idx => $option){
                        $optionval = isset($data['options_vals'])?$data['options_vals'][$idx]:$option;
                        $returnform.="<label class='custom-control custom-radio ".strtolower($option)."'>
                        <input type='[type]' name='[name]' id='id_[name]_".strtolower($option)."' class='custom-control-input' value='".$option."' [required] ".
                        (array_key_exists($name,$values)&&($values[$name]==$option||($values[$name]==''&&$option=='All'))?"checked='checked'":"")." />
                        <span class='custom-control-indicator'></span>
                        <span class='custom-control-description'>".ucwords($optionval)."</span>
                        </label>";
                    }
                }
                else{
                    $returnform.="<input type='[type]' name='[name]' id='id_[name]' class='[class]' placeholder='[placeholder]' value=\"[value]\" [required] />";
                }
                if($data['type']=='file'){$returnform.='</span>';}
                break;
        }
        $replace = array(
            $data['type']=='date'?'text':$data['type'],
            $name,
            ($data['type']=='file'?'form-inline':'form-control'),
            $data['faclass'],
            $data['placeholder'],
            $data['required'],
            (isset($values[$name])?stripslashes($values[$name]):'')
        );
        $returnform = str_ireplace($find,$replace,$returnform);
        $returnform .='</div><br>';
    }
    return $returnform;
}
function validate_form($elements,$values,$table='')
{
    global $db;
    $return='';
    // Unique SEO
    if($table!=''&&isset($values['seotitle']))
    {
        $binds=array($values['seotitle']);
        if(strlen($values['id'])>0){$binds[]=$values['id'];}
        $postQ = $db->prepare('SELECT id FROM '.$table.' WHERE `seotitle`=?'.(strlen($values['id'])>0?' AND `id`!=?':''));
        $postQ->execute($binds);
        $seocheck = $postQ->fetch(PDO::FETCH_ASSOC);
        if($postQ->rowCount()>0)
            {$return.="<br>SEO title already in use. Please choose another.";}
    }

    // Empty required fields
    foreach($elements as $name => $data){
        if($data['required']=='required'&&strlen($values[$name])<1){$return.="<br>".ucwords($name)." is required.";}
    }
    return $return;
}
if (empty($_SESSION['token'])) {
    if (function_exists('mcrypt_create_iv')) {
        $_SESSION['token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    } else {
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}
$csrf_token = $_SESSION['token'];
?>
