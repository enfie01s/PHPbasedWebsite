<?php
/* ********************** HOME ********************** */
    if(count($curpath)==1){
        $posts = $db->query('SELECT * FROM blog_post ORDER BY pub_date DESC');
        $data['posts'] = $posts->fetchAll(PDO::FETCH_ASSOC);

        $tpl->parse('home.html',$data);
    }

/* ********************** VIEW ********************** */ 
    elseif($curpath[1] != 'manage' && $curpath[2] != 'delete'){
        $q = $db->prepare('SELECT * FROM blog_post WHERE `pub_date`=? AND `seotitle`=?');
        $q->execute(array($curpath[1].'-'.$curpath[2].'-'.$curpath[3].' 00:00:00',$curpath[4]));
        $data['post'] = $q->fetch(PDO::FETCH_ASSOC);

        $tpl->parse('view.html',$data);
    }

/* ********************** MANAGE ********************** */ 
    elseif($curpath[1]=='manage'){        
        $post=array();
        $errors = '';
        $new = !isset($safepost['savecontinue'])&&!isset($safepost['save'])&&!isset($curpath[2])?1:0;

        if(!isset($safepost['savecontinue'])&&!isset($safepost['save'])&&isset($curpath[2])){
            $postQ = $db->prepare('SELECT * FROM blog_post WHERE `id`=?');
            $postQ->execute(array($curpath[2]));
            $post = $postQ->fetch(PDO::FETCH_ASSOC);
            $post['pub_date'] = dateform($post['pub_date'],'d/m/Y');
        }

        /* ** POST HANDLER ** */
        elseif(isset($safepost['savecontinue'])||isset($safepost['save'])){

            /* * DELETE * */
            if(isset($safepost['deleterow'])&&strlen($safepost['id'])>0)
            {
                $postQ = $db->prepare('SELECT image FROM blog_post WHERE `id`=?');
                $postQ->execute(array($safepost['id']));
                $post = $postQ->fetch(PDO::FETCH_ASSOC);
                $dbQ = $db->prepare("DELETE FROM blog_post WHERE `id`=?");
                $dbQ->execute(array($safepost['id']));
                if(is_file($_SERVER['DOCUMENT_ROOT']."/".$pagedir."/media/".str_replace('blog/','',$post['image'])))
                {unlink($_SERVER['DOCUMENT_ROOT']."/".$pagedir."/media/".str_replace('blog/','',$post['image']));}
                //need to redirect to home
            }
            /* * ADD/EDIT * */
            else
            {
                /* delete image if clear is ticked */
                if(isset($safepost['image-clear'])&&strlen($safepost['id'])>0){
                    unlink($_SERVER['DOCUMENT_ROOT']."/".$safepost['image-clear']);
                }

                $dbits = explode("/",$safepost['pub_date']);
                $dpath=$dbits[2]."/".$dbits[1]."/".$dbits[0]."/";
                
                /* Image */
                $image='';
                $upload='none';
                if(isset($_FILES['image'])&&$_FILES['image']['error'] == 0){
                    $upload = uploadfile(
                        $_SERVER['DOCUMENT_ROOT']."/".$pagedir."/media/". $dpath."/",
                        $_FILES['image']['name'],
                        $_FILES['image']['tmp_name'],
                        '',
                        '1024',
                        array('jpg','png','gif'));
                    if($upload == 'ok'){
                        $image = $dpath.$_FILES['image']['name'];
                        $safepost['image'] = $image;
                    }else{$errors.='<br>'.$upload;}
                }

                /* Data */
                $safepost['body']=$_POST['body'];
                $post = $safepost;

                $data['formerrors'] = validate_form($postform,$post,'blog_post');

                // Passed tests
                if(strlen($data['formerrors']) < 1){
                    /* update/add to db */
                    $imgup = strlen($image)>0?'`image`=?,':'';
                    $imgadd = strlen($image)>0?'`image`,':'';
                    $binds=array();
                    if(strlen($image)>0){$binds[]=$image;} 
                    $binds[]=$post['title'];
                    $binds[]=$post['seotitle'];
                    $binds[]=$dbits[2]."-".$dbits[1]."-".$dbits[0]." 00:00:00";
                    $binds[]=$post['body'];
                    if(strlen($post['id']) > 0){
                        $binds[]=$post['id'];
                        $dbQ = $db->prepare("UPDATE blog_post SET ".$imgup."`title`=?,`seotitle`=?,`pub_date`=?,`body`=? WHERE `id`=?");
                        $dbQ->execute($binds);
                        $backto=$curpath[0].'/'.$dpath.$post['seotitle'];
                    }
                    else{
                        $dbQ = $db->prepare("INSERT INTO blog_post(".$imgadd."`title`,`seotitle`,`pub_date`,`body`) VALUES (?".str_repeat(',?',count($binds)-1).")");
                        $dbQ->execute($binds);
                        $post['id']=$db->lastInsertId();
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
        }

        /* ** SET UP THE PAGE ** */
        $data['post'] = $post;
        $data['form'] = makeform($postform,$post);

        $tpl->parse('manage.html',$data);
    }

?>