<?php
// ***************************
// 接続
// ***************************
$pdo = connectDb();

// *************************************
// データベース接続
// *************************************
function connectDb(){

    $sqlite = null;

    try {
        $sqlite = new PDO( "sqlite:../{$GLOBALS["dbname"]}" );
    }
    catch ( PDOException $e ) {
        $GLOBALS["error"]["db"] .= $dbname;
        $GLOBALS["error"]["db"] .= " " . $e->getMessage();
        return $sqlite;
    }
    // 接続以降で try ～ catch を有効にする設定
    $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $sqlite;

}