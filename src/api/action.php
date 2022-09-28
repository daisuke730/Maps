<?php
session_start();
include('../functions.php');

// ログインしていない場合は弾く
if(!is_loggedin()) {
  http_response_code(401);
  exit();
}

// クエリがセットされていない場合は弾く
if(!isset($_GET['q']) && !isset($_POST['q'])) {
  http_response_code(400);
  exit();
}

$pdo = connect_to_db();

// 投稿IDからいいね数といいね状態を取得
function get_likes($post_id, $user_id) {
  global $pdo;

  // いいね数を取得
  $sql = "SELECT COUNT(*) AS likes FROM likes WHERE post_id = :post_id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
  $status = $stmt->execute();
  db_error_check($status, $stmt);
  $likes = $stmt->fetch(PDO::FETCH_ASSOC);

  // いいね状態を取得
  $sql = "SELECT COUNT(*) AS liked FROM likes WHERE post_id = :post_id AND user_id = :user_id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $status = $stmt->execute();
  db_error_check($status, $stmt);
  $liked = $stmt->fetch(PDO::FETCH_ASSOC);

  return [
    'like_count' => $likes['likes'],
    'is_liked' => $liked['liked'] > 0
  ];
}

// エラーを返す
function error($message) {
  echo json_encode([
    'error' => $message
  ]);
  exit();
}

// POSTリクエスト
if($_SERVER['REQUEST_METHOD'] === 'POST') {
  switch($_POST['q']) {
    // 投稿へのいいね
    case 'likePost': {
      if (!isset($_POST['post_id'])) {
        http_response_code(400);
        exit();
      }

      $post_id = $_POST['post_id'];
      $user_id = $_SESSION['user_id'];

      // いいね済みかどうか
      $sql = 'SELECT * FROM likes WHERE post_id = :post_id AND user_id = :user_id';
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
      $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      
      // 既にいいねしていた場合は終了
      if ($result) {
        exit();
      }

      // いいね処理
      $sql = "INSERT INTO likes (post_id, user_id, created_at) VALUES (:post_id, :user_id, now())";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
      $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->execute();

      echo json_encode([
        'success' => true
      ]);

      exit();
    }

    // 投稿へのいいねの取り消し
    case 'unlikePost': {
      if (!isset($_POST['post_id'])) {
        http_response_code(400);
        exit();
      }

      $post_id = $_POST['post_id'];
      $user_id = $_SESSION['user_id'];
      $sql = "DELETE FROM likes WHERE post_id = :post_id AND user_id = :user_id";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
      $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->execute();

      echo json_encode([
        'success' => true
      ]);

      exit();
    }

    // 無効なリクエスト
    default: {
      error('Invalid request');
    }
  }
}

// GETリクエスト
if($_SERVER['REQUEST_METHOD'] === 'GET') {
  switch($_GET['q']) {
    // 投稿一覧を取得
    // - pageクエリでオフセットを設定可能 (10件ずつ)
    // - searchクエリで投稿を検索可能
    case 'getPosts': {
      $keyword = isset($_GET['search']) ? $_GET['search'] : '';

      // 投稿を取得
      $sql = "SELECT * FROM todo_table WHERE todo LIKE :keyword ORDER BY updated_at DESC LIMIT 10 OFFSET :offset";
      $stmt = $pdo->prepare($sql);
      $offset = isset($_GET['page']) ? (max((int)$_GET['page'], 1) - 1) * 10 : 0;
      $stmt->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);
      $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach($result as &$post) {
        // いいね数と自分がいいねしているかを取得
        $likeState = get_likes($post['id'], $_SESSION['user_id']);

        // リザルトと統合
        $post = array_merge($post, $likeState);

        // この投稿を編集できるかどうか
        $post['can_edit'] = $post['user_id'] === $_SESSION['user_id'] || $_SESSION['is_admin'] === 1;
      }

      // 投稿の件数を取得
      $sql = "SELECT COUNT(*) AS count FROM todo_table WHERE todo LIKE :keyword";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);
      $stmt->execute();
      $count = $stmt->fetch(PDO::FETCH_ASSOC);

      // 投稿の件数と投稿を返す
      echo json_encode([
        'count' => $count['count'],
        'offset' => $offset,
        'posts' => $result
      ]);

      exit();
    }

    // 投稿を取得
    case 'getPost': {
      if (!isset($_GET['id'])) {
        http_response_code(400);
        exit();
      }

      $post_id = $_GET['id'];
      $user_id = $_SESSION['user_id'];

      // 投稿を取得
      $sql = "SELECT * FROM todo_table WHERE id = :post_id";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      // 投稿が存在しなければエラーを返して終了
      if (!$result) return error('投稿が存在しません');

      // いいね数と自分がいいねしているかを取得
      $likeState = get_likes($post_id, $user_id);

      // リザルトと統合
      $result = array_merge($result, $likeState);

      // この投稿を編集できるかどうか
      $result['can_edit'] = $result['user_id'] === $user_id || $_SESSION['is_admin'] === 1;

      echo json_encode($result);
      exit();
    }

    // 無効なリクエスト
    default: {
      error('Invalid request');
    }
  }
}