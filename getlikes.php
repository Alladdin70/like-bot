<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'vkapi.php';
require_once 'db.php';
        $counter = wallGet()->response->count;
        $ids = array();
        $posts = array();
        $offset = 0;
        while($offset<$counter):
            foreach(wallGet($offset)->response->items as $item):
                array_push($ids, $item->id);
            endforeach;
            $offset += 100;
        endwhile;
        for($i = 0;$i < 100; $i++):
            $post = new Post;
            $post->id = $ids[$i];
            $response = getLikes($post->id);
            $post->count = $response->response->count;
            $post->liked = $response->response->items;
            array_push($posts,$post);
            time_nanosleep(0, 250000000);
        endfor;
        foreach($posts as $pst):
            $tab = new DB;
            $tab->sql = 'SELECT * FROM posts WHERE id = ?';
            $tab->data = array($pst->id);
            $post = $tab->getOne();
            if(!$post):
                addPost($pst);
                $diff = $pst->liked;
            else:
                $diff = getDiffArrays($pst->liked, json_decode($post->liked, true));
                if($diff):
                    updatePost($pst);
                endif;
            endif;
            if($diff):
                echo 'New likes: <br>';
                foreach ($diff as $like):
                    echo $like.'<br>';
                endforeach;
            endif;
        endforeach;
        echo 'done';
        
        class Post{
            public $id;
            public $count;
            public $liked;
        }
        

function addPost($pst){
    $tab = new DB;
    $tab->sql = 'INSERT INTO posts VALUES(?, ?, ?)';
    $tab->data = array($pst->id,$pst->count,json_encode($pst->liked));
    $tab ->execRequest();
    return;        
}

function updatePost($pst){
    $tab = new DB;
    $tab->sql ='UPDATE posts SET liked = ? WHERE id = ?';
    $tab->data = array(json_encode($pst->liked),$pst->id);
    $tab->execRequest();
    $tab->sql ='UPDATE posts SET count = ? WHERE id = ?';
    $tab->data = array($pst->count,$pst->id);
    $tab->execRequest();
    return;
}
        
function getDiffArrays($newArray,$etArray){
    $c = array();
    foreach ($newArray as $newArrayMember):
        if (!in_array($newArrayMember, $etArray)):
            array_push($c, $newArrayMember);
        endif;
    endforeach;
    if(count($c) >0):
        return $c;
    else:
        return FALSE;
    endif;
}         
