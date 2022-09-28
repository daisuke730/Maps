<?php
session_start();
include('functions.php');

// 既にログインしている場合は、投稿一覧へリダイレクト
if (is_loggedin()) {
    header('Location:/posts');
    exit;
}

function login() {
  // POSTリクエストでなければ終了
  if($_SERVER['REQUEST_METHOD'] !== 'POST') return;

  // 入力チェック
  $isValidInput = isset($_POST['username']) && $_POST['username'] !== '' && isset($_POST['password']) && $_POST['password'] !== '';
  if (!$isValidInput) return 'ユーザ名またはパスワードが入力されていません。';

  $username = $_POST['username'];
  $password = $_POST['password'];

  $pdo = connect_to_db();
  $sql = 'SELECT * FROM users_table WHERE username=:username AND password=:password AND is_deleted=0';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':username', $username, PDO::PARAM_STR);
  $stmt->bindValue(':password', $password, PDO::PARAM_STR);
  $status = $stmt->execute();

  // エラーチェック
  db_error_check($status, $stmt);

  $val = $stmt->fetch(PDO::FETCH_ASSOC);

  // ログインに失敗した場合
  if (!$val) return 'ユーザ名またはパスワードに誤りがあります。';

  $_SESSION = array();
  $_SESSION['session_id'] = session_id();
  $_SESSION['is_admin'] = $val['is_admin'];
  $_SESSION['username'] = $val['username'];
  $_SESSION['user_id'] = $val['id'];
  header("Location:/posts");
  exit();
}

$error_message = login();
?>

<?php
$title = "ログイン";
$bgColor = true;
include("components/head.php");
?>

<div class="form-container">
  <div class="ui container">
    <h2 class="ui center aligned">ログイン</h2>
    <?php if ($error_message) echo '<div class="ui red message">' . $error_message . '</div>' ?>
    <form method="POST">
      <div class="ui form">
        <div class="field">
          <label>ユーザ名</label>
          <input type="text" name="username" placeholder="ユーザ名">
        </div>
        <div class="field">
          <label>パスワード</label>
          <input type="password" name="password" placeholder="パスワード">
        </div>
        <button class="ui fluid large teal submit button" type="submit">ログイン</button>
      </div>
    </form>
    <h4>アカウントをお持ちでない場合は<a href="register.php">新規登録</a></h4>
  </div>
</div>

<?php include("components/footer.php"); ?>