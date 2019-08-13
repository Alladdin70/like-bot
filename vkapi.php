<?php
//JSON-СТРОКА ДЛЯ СКРЫТИЯ КЛАВИВАТУРЫ
define('HIDE_KEYBOARD','{"buttons":[],"one_time":true}');
// МЕТОДЫ ВК
define('MESSAGE_SEND','https://api.vk.com/method/messages.send?');
define('GET_HISTORY','https://api.vk.com/method/messages.getHistory?');
define('GET_MSG_UPLOAD_SERVER','https://api.vk.com/method/photos.getMessagesUploadServer?');
define('PHOTO_MSG_SAVE','https://api.vk.com/method/photos.saveMessagesPhoto?server=');
define('WALL_GET','https://api.vk.com/method/wall.get?');
define('GET_LIKES','https://api.vk.com/method/likes.getList?');
//ДАННЫЕ ДЛЯ API
define('VERSION',5.101);
define('GROUP_ID', 137527828);
define('SERVICE_TOKEN','73fa967c73fa967c73fa967c3f739063d3773fa73fa967c2eb16f7207f9c83a78eacf51');
define('GROUP_TOKEN','19836e2c377bd86f0b11ae2abfdfaa9506d2b756dd817fa80e480d3a6e9829a13445118c512141ff1a6b8');
define('ADMIN_TOKEN','dff2cf10aa56dd7f87bb008d6f0c1cf5da72f979152f0c7123fc801c93d94f5d0b8e65bbd3a03f4fefd73');

/*****************************************************************************
 * 
 * 
 * 
 ***************************************************************************** 
 */

function wallGet($offset=0){
    $param = array(
        'domain' => 'youreurotrip',
        'offset' => $offset,
        'access_token' => ADMIN_TOKEN,
        'v'=> VERSION,
        'count' => 100);
    $response =file_get_contents(WALL_GET.http_build_query($param));
    return json_decode($response);
}

function getLikes($id, $offset=0){
    $param = array(
        'owner_id' => -137527828,
        'type' => 'post',
        'friends_only' =>false,
        'item_id' => $id,
        'offset' => $offset,
        'access_token' => ADMIN_TOKEN,
        'v'=> VERSION,
        'count' => 100);
    $response =file_get_contents(GET_LIKES.http_build_query($param));
    return json_decode($response);
}