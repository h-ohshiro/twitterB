//認証用インスタンスの生成
var twitter = TwitterWebService.getInstance(
  "APIキー", //API Key
  "APIシークレットキー" //API secret key
);

//アプリを連携認証する
function authorize() {
  twitter.authorize();
}

//認証を解除する
function reset() {
  twitter.reset();
}

//認証後のコールバック
function authCallback(request) {
  return twitter.authCallback(request);
}
