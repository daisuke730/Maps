<?php
// 環境変数
// 本番環境の情報は入力しないでください

// データベース
$database = array(
  'host' => 'localhost',
  'port' => 3306,
  'user' => 'root',
  'pass' => '',
  'dbName' => 'dec_todo',
);

// env.php外から環境変数を取得する関数
function get_env($key) {
  global $database;

  $env_list = array(
    'database' => $database,
  );

  return $env_list[$key];
}
?>