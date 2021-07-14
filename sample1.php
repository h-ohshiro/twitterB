<?php
//GoogleAPIライブラリを読み込む
require_once (dirname(__FILE__) . '/vendor/autoload.php');
//取得したAPIキーを定数にセットする
const API_KEY = "APIキー";

//認証を行う
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName("youtube-api-test");
    $client->setDeveloperKey(API_KEY);
    return $client;
}

//動画を取得する.
function searchVideos()
{
    $youtube = new Google_Service_YouTube(getClient());
    //ここに取得したいYouTubeのチャンネルIDを入れる
    $params['channelId'] = 'UCZs079QczpW185cfLHqmBpw';
    $params['type'] = 'video';
    $params['maxResults'] = 15;
    $params['order'] = 'date';
    try {
        $searchResponse = $youtube->search->listSearch('snippet', $params);
    } catch (Google_Service_Exception $e) {
        echo htmlspecialchars($e->getMessage());
        exit;
    } catch (Google_Exception $e) {
        echo htmlspecialchars($e->getMessage());
        exit;
    }
    foreach ($searchResponse['items'] as $search_result) {
        $videos[] = $search_result;
        $touroku[] = $search_result['items'][0]['statistics']['viewCount'];
    }
    return $videos;
}

$videos = searchVideos();

//取得した動画のサムネを表示してみる
foreach ($videos as $video) {
    echo '<img src="' . $video['snippet']['thumbnails']['high']['url']. '" />';
}
var_dump($touroku);
// foreach ($touroku as $touro) {
//     echo $touro;
// }
