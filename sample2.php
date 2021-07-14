<?php
define('YOUTUBE_API_KEY', 'APIキー'); // APIキー (Google Developer Consoleから取得したものをセットしてください)

function json_get($url, $query = array(), $assoc = false) { // JSONデータ取得用
    if ($query) $url .= ('?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986));

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url); // URL
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // リクエスト先が https の場合、証明書検証をしない (環境によって動作しない場合があるため)
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // curl_exec() 経由で応答データを直接取得できるようにする
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); // 接続タイムアウトの秒数
    $responseString = curl_exec($curl); // 応答データ取得
    curl_close($curl);
    return ($responseString !== false) ? json_decode($responseString, $assoc) : false;
}
function h($value, $encoding = 'UTF-8') { return htmlspecialchars($value, ENT_QUOTES, $encoding); } // HTMlエスケープ出力用
function eh($value, $encoding = 'UTF-8') { echo h($value, $encoding); } // 同上


$response = json_get('https://www.googleapis.com/youtube/v3/search', array(
    'key' => YOUTUBE_API_KEY,
    'channelId' => 'UCZs079QczpW185cfLHqmBpw', // チャンネルID (チャンネルで絞り込む場合)
    // 'q' => 'テスト', // 検索キーワード (キーワードで絞り込む場合)
    'part' => 'snippet', // 取得するデータの種類 (タイトルや画像を含める場合はsnippet)
    'order' => 'date', // 日時降順
    'maxResults' => 50, // 検索数 (5～50)
    'type' => 'video', // 結果の種類 (channel,playlist,video)
), true);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>PHP による YouTube Data API v3 を用いた検索の例</title>
</head>
<body>
    <h1>akiの動画リスト</h1>
    <?php if ($response === false || isset($response['error'])) { ?>
        動画情報が取得できませんでした。
    <?php } elseif (count($response['items']) == 0) { ?>
        検索結果が0件でした。
    <?php } else { ?>
        <?php foreach ($response['items'] as $item) {
            $img = $item['snippet']['thumbnails']['default']; // 画像情報 (default, medium, highの順で画像が大きくなります)
            $id = $item['id']['videoId'];

            $t = new DateTime($item['snippet']['publishedAt']);
            $t->setTimeZone(new DateTimeZone('Asia/Tokyo'));
            $publishedAt = $t->format('Y/m/d H:i:s'); // 投稿日時 (日本時間)
            ?>
            <!-- <?php echo json_encode($item, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?> -->
            <a href="https://www.youtube.com/watch?v=<?php eh($id) ?>"><img src="<?php eh($img['url']) ?>"></a><br>
            <a class="item-title" href="https://www.youtube.com/watch?v=<?php eh($id) ?>"><?php eh($item['snippet']['title']) ?></a><br>
            <span class="item-publishedAt"><?php eh($publishedAt) ?></span>
            <hr>
        <?php } ?>
    <?php } ?>

</body>
</html>
