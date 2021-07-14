function Tweet() {
  //シート定義
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheetName = "sheet2";
  var srcSheet = ss.getSheetByName(sheetName);
  //データを貼り付けるシートをクリア
  srcSheet.clear();
  //  項目を書き込む
  srcSheet.getRange(1, 1).setValue("チャンネル名");
  srcSheet.getRange(1, 2).setValue("動画名称");
  srcSheet.getRange(1, 3).setValue("video_ID");

  var channelId = "チャンネルID";
  var APIkey = "APIキー";
  var maxResults = 50;
  var json = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=" + channelId + "&maxResults=" + maxResults + "&order=date&key=" + APIkey;
  //  jsonファイルを取得
  var jsonGet = UrlFetchApp.fetch(json).getContentText();
  //  パースしvideIDのみ取得
  var videoTotalCount = JSON.parse(jsonGet).pageInfo.totalResults;

  for (var i = 0; i < maxResults; i++) {
    var channelTitle = JSON.parse(jsonGet).items[i].snippet.channelTitle;
    var title = JSON.parse(jsonGet).items[i].snippet.title;
    var videoId = JSON.parse(jsonGet).items[i].id.videoId;
    var videoUrl = "https://www.youtube.com/watch?v=" + videoId;
    var tags = "#桃鉄 #テトリス99 #スプラトゥーン2"; //tweetに表示したい任意のタグを入力する。
    srcSheet.getRange(i + 2, 1).setValue(channelTitle);
    srcSheet.getRange(i + 2, 2).setValue(title);
    srcSheet.getRange(i + 2, 3).setValue(videoUrl);
    srcSheet.getRange(i + 2, 4).setValue(tags);
  }

  //Twitterに投稿するメッセージを作成する
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("sheet2");
  var lastRow = sheet.getLastRow();

  //2行目～最終行の間で、ランダムな行番号を算出する
  var row = Math.ceil(Math.random() * (lastRow - 1)) + 1;

  //ランダムに算出した行番号のタイトルとURLを取得
  var title = sheet.getRange(row, 2).getValue();
  var url = sheet.getRange(row, 3).getValue();
  var tag = sheet.getRange(row, 4).getValue();

  var header = channelTitle + "の本日のおすすめyoutube動画はコチラ▽";

  var postMessage = header + "\n\n" + title + "\n\n" + url + "\n\n" + tag;
  Logger.log(postMessage);

  var service = twitter.getService();
  var endPointUrl = "https://api.twitter.com/1.1/statuses/update.json";
  var response = service.fetch(endPointUrl, {
    method: "post",
    payload: {
      status: postMessage,
    },
  });
}
