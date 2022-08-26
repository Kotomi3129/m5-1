<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>misson_5-1</title>
</head>
 <body>

    <?php
    //データベースに接続
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    $sql = "CREATE TABLE IF NOT EXISTS m5_1"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "created_on DATETIME,"
    . "password TEXT"
    .");";
    $stmt = $pdo->query($sql);
    
    //名前、コメントのフォームが空でないとき、POSTデータの受け取り
    //htmlspecialchars…ENT_QUOTESは掲示板のコメント入力時に「不正なhtmlタグの埋め込み」を防止する役割
    if (!empty($_POST["name"]) && !empty($_POST["comment"])) {
    $name = htmlspecialchars($_POST["name"], ENT_QUOTES);
    $comment = htmlspecialchars($_POST["comment"], ENT_QUOTES);
    $password1 = htmlspecialchars($_POST["password1"], ENT_QUOTES);
    

     if(empty($_POST["editNo"])){
    //以下新規投稿機能
    //PHPでデータベース内のテーブル読み込み、POSTで受け取った内容を書き込み
    $DATETIME = new DateTime();
	$DATETIME = $DATETIME->format('Y-m-d H:i:s');
    $sql = $pdo -> prepare("INSERT INTO m5_1 (name, comment, created_on, password) VALUES (:name, :comment, :created_on, :password)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql -> bindParam(':created_on',$DATETIME,PDO::PARAM_STR);
    $sql -> bindParam(':password',$password1,PDO::PARAM_STR);
    $sql -> execute();
     }else{
    //以下編集機能（update）
    $id = $_POST["editNo"];; //変更する投稿番号
    $name = htmlspecialchars($_POST["name"], ENT_QUOTES);
    $comment = htmlspecialchars($_POST["comment"], ENT_QUOTES); 
    $password = htmlspecialchars($_POST["password1"], ENT_QUOTES);//パスワードも編集する？
     $sql = 'UPDATE m5_1 SET name=:name,comment=:comment, password=:password WHERE id=:id';
     $stmt = $pdo->prepare($sql);
     $stmt->bindParam(':name', $name, PDO::PARAM_STR);
     $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
     $stmt->bindParam(':password', $password, PDO::PARAM_STR);
     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
     $stmt->execute();
     }
    }
    
    // 受け取ったレコードの削除
    if (isset($_POST["delete_id"])&&!empty($_POST["password2"])) {
     $delete_id = $_POST["delete_id"];
     $password2 = $_POST["password2"];
     $sql = 'SELECT * FROM m5_1 WHERE id=:delete_id;';
     $stmt = $pdo->prepare($sql);                 
     $stmt->bindParam(':delete_id', $delete_id, PDO::PARAM_INT); 
     $stmt->execute();                             // ←SQLを実行する。
     $results = $stmt->fetchAll(); 
     foreach ($results as $row){
         if($row['password']==$password2){
     $sql = "delete from m5_1 where id = :delete_id;";
     $stmt = $pdo->prepare($sql);
     $stmt -> bindParam(":delete_id", $delete_id, PDO::PARAM_INT);
     $stmt -> execute();
         }
     }  
    }
    
    //受け取ったレコードの編集
    if (isset($_POST["update_id"])&&!empty($_POST["password3"])){
     $update_id = $_POST["update_id"];
     $password3 = $_POST["password3"];
    //編集するデータを取得する
     $sql = 'SELECT * FROM m5_1 WHERE id=:update_id;';
     $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
     $stmt->bindParam(':update_id', $update_id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
     $stmt->execute();                             // ←SQLを実行する。
     $results = $stmt->fetchAll(); 
     foreach ($results as $row){
       //$rowの中にはテーブルのカラム名が入る
      if ($row['id']== $update_id){//$edi[0]、分割したときに一番最初に出てくる要素である投稿番号が、$edinumと一致するならば
      if($row['password']== $password3){//投稿した際のパスワードと編集のパスワードが一致すれば
       $updatename = $row['name'];
       $updatecomment = $row['comment'];
            //既存の投稿フォームに、上記で取得した「名前」と「コメント」の内容が既に入っている状態で表示させる
            //formのvalue属性で対応
      }
      }
     }
    }
    
    ?>
    
     <form action = "" method = "post">       
        <label for="name">名前</label>
            <input type = "text" name = "name" value="<?php echo $updatename ?? ''; ?>"> 
        <label for="comment">コメント</label>
            <input type = "text" name = "comment" value="<?php echo $updatecomment ?? ''; ?>">
            <input type="hidden" name="editNo" value="<?php if(isset($update_id)) {echo $update_id;} ?>">
        <label for = "password">パスワード</label>
            <input type = "text" name = "password1">
            <input type = "submit" name = "submit"> <br> 
        <label for="delete">削除</label>
            <input type = "text" name = "delete_id" placeholder = "削除対象番号">
        <label for = "password2">パスワード</label>  
            <input type = "text" name = "password2"> 
            <input type = "submit"  value = "削除"><br>
        <label for="update">編集</label>
            <input type = "text" name = "update_id" placeholder = "編集対象番号">
        <label for = "password3">パスワード</label>
            <input type = "text" name = "password3">
            <input type = "submit" value = "編集"><br>
    </form>    
    
    <?php
    //表示
    $sql = 'SELECT * FROM m5_1';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
     foreach ($results as $row){
         //$rowの中にはテーブルのカラム名が入る
         echo $row['id'].',';
         echo $row['name'].',';
         echo $row['comment'].',';
         echo $row['created_on'].'<br>';
     echo "<hr>";
     }
    ?>
    </body>
</html>