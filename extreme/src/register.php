<?php
include('functions.php');

function register() {
  // POSTリクエストでなければ終了
  if($_SERVER['REQUEST_METHOD'] !== 'POST') return;

  // 入力チェック
  $isValidInput = isset($_POST['username']) && $_POST['username'] !== '' && isset($_POST['password']) && $_POST['password'] !== '';
  if (!$isValidInput) return 'ユーザ名またはパスワードが入力されていません。';

  $username = $_POST["username"];
  $password = $_POST["password"];

  $pdo = connect_to_db();
  $sql = 'SELECT COUNT(*) FROM users_table WHERE username=:username';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':username', $username, PDO::PARAM_STR);
  $status = $stmt->execute();

  // エラーチェック
  db_error_check($status, $stmt);

  // すでに登録されていた場合
  if ($stmt->fetchColumn() > 0) return 'そのユーザー名は使用されています。';

  $sql = 'INSERT INTO users_table(id, username, password, is_admin, is_deleted, created_at, updated_at) VALUES(NULL, :username, :password, 0, 0, sysdate(), sysdate())';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':username', $username, PDO::PARAM_STR);
  $stmt->bindValue(':password', $password, PDO::PARAM_STR);
  $status = $stmt->execute();

  // エラーチェック
  db_error_check($status, $stmt);

  header("Location:login.php");
  exit();
}

$error_message = register();
?>

<?php
$title = "ユーザ登録";
$bgColor = true;
include("components/head.php");
?>

<div class="form-container">
  <div class="ui container">
    <h2 class="ui center aligned">新規登録</h2>
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
        <button class="ui fluid large teal submit button" type="submit">登録</button>
      </div>
    </form>
    <h4>既にアカウントがある場合は<a href="login.php">こちらからログイン</a></h4>
  </div>
</div>

<?php include("components/footer.php"); ?>