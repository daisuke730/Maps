# Extreme

## 開発環境の構築
このプロジェクトを動作させるためにはPHPおよびMySQLが動作する環境が必要です。  
Windows/MacではXAMPPを使用することで簡単に環境を構築することができます。  

### 公式サイトからダウンロードする
[XAMPPのダウンロードページ](https://www.apachefriends.org/jp/download.html)に移動し、インストーラーをダウンロードします。  
Windows, Mac共にバージョン8.1.xをダウンロードし、インストールを行ってください。  
**また、Macの場合はファイルサイズが小さい方をダウンロードしてください。**

### サーバーを起動する
XAMPP Control Panelより、Apache, MySQLをStartします。  
起動した状態で、以下のページにアクセスできれば正常に動作しています。
- http://localhost/
- http://localhost/phpmyadmin

## データベースの作成
ローカルで起動している[phpMyAdminにアクセス](http://localhost/phpmyadmin)し、データベースを作成します。  
下記の方法に従ってデータベースをインポートするか、手動で作成してください。

### データベースをインポートする (推奨)
まずはじめに、任意のディレクトリにこのリポジトリをクローンします。
```bash
$ git clone https://github.com/daisuke730/extreme.git
```
クローン後、以下のディレクトリにデータベースファイルが存在することを確認してください。  
`extreme/assets/dec_todo.sql`  
phpMyAdminのトップページにある「インポート」タブから、上記のファイルを選択し、インポートします。  
これでデータベースの作成は完了です。

### データベースを手動で作成する
> **Note**  
> データベースをインポートした場合は以下の操作は必要ありません

1. phpMyAdminのトップページにある左のカラムから「新規作成」をクリックします。
2. データベース名に「`dec_todo`」と入力し、「`utf8mb4_general_ci`」を「`utf8mb4_unicode_ci`」に変更します。
3. 画面左のカラムに「dec_todo」が表示されるのでクリックします。
4. 「新しいテーブルを作成」をクリックし、テーブル名を「`todo_table`」に、カラム数を「`6`」に指定してから作成ボタンを入力します。
5. 以下に従ってテーブルを作成します。  
   |名前|タイプ|長さ|備考|
   |----|----|----|----|
   |id|INT|12|`A_I`にチェックを付け、インデックスを`PRIMARY`に設定|
   |todo|VARCHAR|128||
   |url|VARCHAR|4096||
   |created_at|DATETIME|||
   |updated_at|DATETIME|||
   |user_id|int|12||
6. 「新しいテーブルを作成」をクリックし、テーブル名を「`users_table`」に、カラム数を「`7`」に指定してから作成ボタンを入力します。
7. 以下に従ってテーブルを作成します。  
   |名前|タイプ|長さ|備考|
   |----|----|----|----|
   |id|INT|12|`A_I`にチェックを付け、インデックスを`PRIMARY`に設定|
   |username|VARCHAR|128||
   |password|VARCHAR|128||
   |is_admin|INT|1|管理者ユーザーと一般ユーザーの識別に使用|
   |is_deleted|INT|1|論理削除に使用|
   |created_at|DATETIME|||
   |updated_at|DATETIME|||
8. 「新しいテーブルを作成」をクリックし、テーブル名を「`likes`」に、カラム数を「`3`」に指定してから作成ボタンを入力します。
9. 以下に従ってテーブルを作成します。  
   |名前|タイプ|長さ|備考|
   |----|----|----|----|
   |post_id|INT|12||
   |user_id|INT|12||
   |created_at|DATETIME|||