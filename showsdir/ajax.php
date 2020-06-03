<?php
/*
@login_required
def dl(request):
    if request.method == 'POST' and request.is_ajax():
        epi = request.POST.get('epi')
        episode = Episode.objects.get(pk=epi)
        episode.downloaded = timezone.now()
        #if episode.rssdate is None:
        #    episode.rssdate = dt_1000
        episode.save()
        data = {'downloaded': ', <b>Downloaded:</b> ' + episode.downloaded_pretty(),'id': epi}
        return JsonResponse(data)

@login_required
def dlm(request):
    if request.method == 'POST' and request.is_ajax():
        movid = request.POST.get('id')
        mov = Media.objects.get(pk=movid)
        mov.got = 1
        mov.save()
        data = {'id': movid}
        return JsonResponse(data)
        */
include '../conf.php';
if(isset($_POST['epi']))
{   
    $q=$db->prepare("UPDATE shows_episodes SET `downloaded`=NOW() WHERE `id`=?");
    $q->execute(array($_POST['epi'])); 
    //echo array('downloaded'=>', <b>Downloaded:</b> ' + date('M d Y'),'id': $_POST['epi']};  
}
if(isset($_POST['movieid']))
{   
    $q=$db->prepare("UPDATE shows_movies SET `got`=1 WHERE `id`=?");
    $q->execute(array($_POST['movieid']));   
}
?>