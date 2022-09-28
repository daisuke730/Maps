<?php
session_start();
include('../functions.php');
check_session_id();

// IDが提供されているかどうかをチェック
if (!isset($_GET['id'])) {
    // されていなければリダイレクト
    header('Location:/posts');
    exit();
}

function details() {
    $id = $_GET['id'];
    $pdo = connect_to_db();

    // 投稿を取得
    $sql = 'SELECT * FROM todo_table WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $status = $stmt->execute();

    // エラーチェック
    db_error_check($status, $stmt);

    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    // 投稿が存在しない場合はエラーを出す
    if (!$record) return '投稿が見つかりませんでした';
}

$error_message = details();
?>

<?php
$title = "投稿詳細";
$bgColor = true;
include("../components/head.php");
?>

<div class="form-container">
    <div class="ui container">
        <h2>投稿詳細</h2>
        <?php if ($error_message) echo '<div class="ui red message">' . $error_message . '</div>' ?>
        <?php if ($error_message) echo '<div class="hidden">' ?>
        <h3 id="post-title"></h3>
        <div class="detail-timeview">
            <span>投稿: <span id="post-created-at"></span></span>
            <span>最終更新: <span id="post-updated-at"></span></span>
        </div>

        <a id="post-url" target="_blank" rel="noopener noreferrer">このルートを見る</a>

        <div id="like-button"></div>
        <?php if ($error_message) echo '</div>' ?>
    </div>
</div>

<script src="/js/api.js"></script>
<script src="/js/templates.js"></script>
<script src="/js/detail_page.js"></script>
<?php include("../components/footer.php"); ?>