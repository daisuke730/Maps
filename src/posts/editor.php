<?php
session_start();
include('../functions.php');
check_session_id();

// 編集モードに入るかどうか
$is_editmode = $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']);

function get() {
  // idが指定されていなければ終了
  if (!isset($_GET['id'])) return;

  $id = $_GET['id'];
  $pdo = connect_to_db();
  $sql = 'SELECT * FROM todo_table WHERE id=:id';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':id', $id, PDO::PARAM_INT);
  $status = $stmt->execute();

  // エラーチェック
  db_error_check($status, $stmt);

  $record = $stmt->fetch(PDO::FETCH_ASSOC);
  return $record;
}

function post() {
  // POSTリクエストでなければ終了
  if($_SERVER['REQUEST_METHOD'] !== 'POST') return;

  // 入力チェック
  if (!isset($_POST['todo']) || $_POST['todo'] == '' || !isset($_POST['url']) || $_POST['url'] == '' || !isset($_POST['id'])) return '入力が不足している箇所があります。';

  $id = (int)$_POST['id'];
  $todo = $_POST['todo'];
  $url = $_POST['url'];

  // DB接続
  $pdo = connect_to_db();

  // 新規投稿か編集かで処理を分ける
  if($id === -1) {
    // 新規投稿
    $sql = 'INSERT INTO todo_table(id, todo, url, created_at, updated_at, user_id) VALUES(NULL, :todo, :url, now(), now(), :user_id)';
  } else {
    // 編集

    // 投稿者と編集しようとしている人が同一か (または管理者か) 判別
    $sql = 'SELECT * FROM todo_table WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();

    // エラーチェック
    db_error_check($status, $stmt);

    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($record['user_id'] !== $_SESSION['user_id'] && $_SESSION['is_admin'] !== 1) {
      return '投稿者以外は編集できません。';
    }

    $sql = 'UPDATE todo_table SET todo=:todo, url=:url, updated_at=now() WHERE id=:id';
  }

  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':todo', $todo, PDO::PARAM_STR);
  $stmt->bindValue(':url', $url, PDO::PARAM_STR);

  // 新規投稿の場合はuser_idをバインド
  if($id === -1) {
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
  }

  // 編集の場合はIDをバインド
  if($id !== -1) {
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
  }

  $status = $stmt->execute();

  // エラーチェック
  db_error_check($status, $stmt);

  header("Location:.");
  exit();
}

$error_message = $_SERVER['REQUEST_METHOD'] === 'POST' ? post() : null;
$record = $_SERVER['REQUEST_METHOD'] === 'GET' ? get() : null;
?>

<?php
$title = $is_editmode ? '投稿を編集' : '投稿を作成';
$bgColor = true;
include("../components/head.php");
?>

<div class="form-container">
  <div class="ui container">
    <h2 class="ui center aligned"><?= $is_editmode ? '投稿を編集' : '新しい投稿を作成' ?></h2>
    <?php if ($error_message) echo '<div class="ui red message">' . $error_message . '</div>' ?>
    <form method="POST">
      <div class="ui form">
        <div class="field">
          <label>URL</label>
          <input id="url-input" type="url" name="url" placeholder="URL" value="<?= $is_editmode ? $record['url'] : '' ?>">
        </div>
        <p>出発地と目的地はURLを貼り付けると自動的に入力されます。</p>
        <p>(この機能は開発途中のため、想定通りに動作しないことがあります。)</p>
        <div class="ui two column doubling grid">
          <div class="column">
            <div class="field">
              <label>出発地</label>
              <input id="start-point-name" type="text" name="start" placeholder="出発地">
            </div>
          </div>
          <div class="column">
            <div class="field">
              <label>目的地</label>
              <input id="end-point-name" type="text" name="end" placeholder="目的地">
            </div>
          </div>
        </div>
        <input id="route-name" type="hidden" name="todo">
        <input type="hidden" name="id" value="<?= $is_editmode ? $record['id'] : -1 ?>">
        <button class="ui fluid large teal submit button" type="submit"><?= $is_editmode ? '編集' : '投稿' ?></button>
      </div>
    </form>
  </div>
</div>

<script src="/js/url_parser.js"></script>
<script src="/js/post_page.js"></script>
<?php include("../components/footer.php"); ?>