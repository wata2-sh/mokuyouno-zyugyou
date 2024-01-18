<?php
$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');
if (isset($_POST['body'])) {
  // POSTで送られてくるフォームパラメータ body がある場合

  $image_filename = null;
if (!empty($_POST['image_base64'])) {
    // 先頭の data:~base64, のところは削る
    $base64 = preg_replace('/^data:.+base64,/', '', $_POST['image_base64']);

    // base64からバイナリにデコードする
    $image_binary = base64_decode($base64);
 $image_filename = strval(time()) . bin2hex(random_bytes(25)) . '.jpg';
    $filepath =  '/var/www/upload/image/' . $image_filename;
 file_put_contents($filepath, $image_binary);
  }
$insert_sth = $dbh->prepare("INSERT INTO bbs_entries (body, image_filename) VALUES (:body, :image_filename)");
  $insert_sth->execute([
    ':body' => $_POST['body'],
    ':image_filename' => $image_filename,
  ]);
  // 処理が終わったらリダイレクトする
  // リダイレクトしないと，リロード時にまた同じ内容でPOSTすることになる
  header("HTTP/1.1 302 Found");
  header("Location: ./kadai.php");
  return;
}
// いままで保存してきたものを取得
$select_sth = $dbh->prepare('SELECT * FROM bbs_entries ORDER BY created_at DESC');
$select_sth->execute();
// bodyのHTMLを出力するための関数を用意する
function bodyFilter (string $body): string
{
    $body = htmlspecialchars($body); // エスケープ処理
    $body = nl2br($body); // 改行文字を<br>要素に変換
    // >>1 といった文字列を該当番号の投稿へのページ内リンクとする (レスアンカー機能)
    // 「>」(半角の大なり記号)は htmlspecialchars() でエスケープされているため注意
    $body = preg_replace('/&gt;&gt;(\d+)/', '<a href="#entry$1">&gt;&gt;$1</a>', $body);
    return $body;
}
?>
<head>
  <title>画像投稿できる掲示板</title>
</head>

<form method="POST" action="./kadai.php"><!-- base64のテキストで画像を送ることになるのでenctypeは外しておきましょう -->
  <textarea name="body"></textarea>
  <div style="margin: 1em 0;">
    <input type="file" accept="image/*" name="image" id="imageInput">
<div id="imagePreviewArea" style="display: none;">
      <div style="display: flex; align-items: start; margin: 1em 0;">
        <span style="margin-right: 1em;">プレビュー:</span>
        <canvas id="imagePreviewCanvas" style=""></canvas>
      </div>
    </div>
  </div>
  <input id="imageBase64Input" type="hidden" name="image_base64"><!-- base64を送る用のinput (非表示) -->
  <canvas id="imageCanvas" style="display: none;"></canvas><!-- 画像縮小に使うcanvas (非表示) -->
  <button type="submit">送信</button>
</form>

<hr>
<?php foreach($select_sth as $entry): ?>
  <dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
    <dt id="entry<?= htmlspecialchars($entry['id']) ?>">ID</dt>
    <dd><?= $entry['id'] ?></dd>
    <dt>日時</dt>
    <dd><?= $entry['created_at'] ?></dd>
    <dt>内容</dt>
    <dd>
      <?= bodyFilter($entry['body']) ?>
      <?php if(!empty($entry['image_filename'])): // 画像がある場合は img 要素を使って表示 ?>
      <div>
        <img src="/image/<?= $entry['image_filename'] ?>" style="max-height: 10em;">
      </div>
      <?php endif; ?>
    </dd>
  </dl>
<?php endforeach ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const imageInput = document.getElementById("imageInput");
  const previewArea = document.getElementById("imagePreviewArea");
  const previewCanvas = document.getElementById("imagePreviewCanvas");
  imageInput.addEventListener("change", () => {
// プレビューエリアを一旦非表示に
    previewArea.style.display = 'none';

    if (imageInput.files.length < 1) {
      // 未選択の場合
      return;
    }
const file = imageInput.files[0];
    if (!file.type.startsWith('image/')){ // 画像でなければスキップ
      return;
    }
 const imageBase64Input = document.getElementById("imageBase64Input"); // base64を送るようのinput
    const canvas = document.getElementById("imageCanvas"); // 描画するcanvas
    const reader = new FileReader();
    const image = new Image();
    reader.onload = () => { // ファイルの読み込み完了したら動く処理を指定
      image.onload = () => { // 画像として読み込み完了したら動く処理を指定

        // 元の縦横比を保ったまま縮小するサイズを決めてcanvasの縦横に指定する
        const originalWidth = image.naturalWidth; // 元画像の横幅
        const originalHeight = image.naturalHeight; // 元画像の高さ
        const maxLength = 2000; // 横幅も高さも2000px以下に縮小するものとする
        if (originalWidth <= maxLength && originalHeight <= maxLength) { // どちらもmaxLength以下の場合そのまま
            canvas.width = originalWidth;
            canvas.height = originalHeight;
        } else if (originalWidth > originalHeight) { // 横長画像の場合
            canvas.width = maxLength;
            canvas.height = maxLength * originalHeight / originalWidth;
        } else { // 縦長画像の場合
            canvas.width = maxLength * originalWidth / originalHeight;
            canvas.height = maxLength;
        }

        // canvasに実際に画像を描画 (canvasはdisplay:noneで隠れているためわかりにくいが...)
        const context = canvas.getContext("2d");
        context.drawImage(image, 0, 0, canvas.width, canvas.height);

        // canvasの内容をjpeg形式のbase64に変換しinputのvalueに設定
        imageBase64Input.value = canvas.toDataURL('image/jpeg', 0.9);
       // プレビューエリアの display:none (非表示) を解除
        previewArea.style.display = '';
        // プレビューcanvasの高さを200px固定として、元画像の縦横比から横幅を設定
        previewCanvas.height = canvas.height = '200';
        previewCanvas.width = previewCanvas.height * originalWidth / originalHeight;
        // プレビューcanvasへ画像を描画
        const previewContext = previewCanvas.getContext("2d");
        previewContext.drawImage(image, 0, 0, previewCanvas.width, previewCanvas.height);
};
      image.src = reader.result;
    };
    reader.readAsDataURL(file);
  });
});
</script>
