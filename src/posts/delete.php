<?php
session_start();
include('../functions.php');
check_session_id();

// IDクエリが存在するかチェック
if (!isset($_GET['id'])) {
    // IDクエリが存在しない場合は一覧ページに戻る
    header("Location:./");
    exit();
}

// データ受け取り
$id = $_GET['id'];
$pdo = connect_to_db();

// ページの所有者を確認
$sql = 'SELECT * FROM todo_table WHERE id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

// エラーチェック
db_error_check($status, $stmt);

$record = $stmt->fetch(PDO::FETCH_ASSOC);
if ($record['user_id'] !== $_SESSION['user_id'] && $_SESSION['is_admin'] !== 1) {
    // ページの所有者とログインユーザーが異なる場合は一覧ページに戻る
    header("Location:./");
    exit();
}

// データ削除
$sql = 'DELETE FROM todo_table WHERE id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_STR);
$status = $stmt->execute();

// エラーチェック
db_error_check($status, $stmt);

header("Location:./");
exit();
?>