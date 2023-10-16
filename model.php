<?php

// ***********************************
// 更新処理
// ***********************************
function update_row() {

    global $pdo;

    foreach ($_POST as $key => $value) {
        if ( preg_match("/^[0-9]+$/", $key) ) {
            if ( $_POST[$key] != $_POST["sv" . $key] ) {
                $sql =<<<UPDATE_SQL
                UPDATE 社員マスタ SET
                    給与 = :kyuyo
                WHERE 社員コード = :scode
UPDATE_SQL;

                try {
                    // SQL 文の準備
                    $stmt = $pdo->prepare($sql);

                    // バインド
                    $stmt->bindValue( ':kyuyo', $_POST[$key], PDO::PARAM_INT );
                    $stmt->bindValue( ':scode', $key, PDO::PARAM_STR );

                    // 完成した SQL の実行
                    $stmt->execute();

                }
                catch ( PDOException $e ) {
                    $GLOBALS["error"]["db"] = $e->getMessage();
                    return false;
                }

            }
        }
    }

    if ( $_POST["sql"] != "" ) {
        try {
            // SQL の実行
            $pdo->exec($_POST["sql"]);

        }
        catch ( PDOException $e ) {
            $error = $e->getMessage();
            return false;
        }

    }

}

// ***********************************
// 画面用テーブル要素文字列作成
// ***********************************
function get_table($statement) {

    $html = "";
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {

        if ( $row["性別"]  == "男性" ) {
            $class = "text-primary";
        }
        else {
            $class = "text-danger";
        }

        $kyuyo = $row["給与"];
        if ( $row["手当"] == "" ) {
            $teate = "";
        }
        else {
            $teate = number_format($row["手当"]+0);
        }
        $html .=<<<HTML
        <tr class="row_data">
            <td class="pre">{$row["社員コード"]}</td>
            <td class="pre">{$row["氏名"]}</td>
            <td class="pre">{$row["フリガナ"]}</td>
            <td class="pre">{$row["所属"]}</td>
            <td class="{$class} pre">{$row["性別"]}</td>
            <td>
                <input
                    class="w100"
                    pattern="[0-9]+"
                    type="text"
                    name="{$row["社員コード"]}"
                    value="{$kyuyo}">
                <input
                    type="hidden"
                    name="sv{$row["社員コード"]}"
                    value="{$kyuyo}">
            </td>
            <td class="text-end pre">{$teate}</td>
            <td class="pre">{$row["管理者"]}</td>
            <td class="pre">{$row["生年月日"]}</td>
        </tr>
HTML;

    }

    return $html;

}

// **************************
// デバッグ表示
// **************************
function debug_print() {

    print "<pre class=\"m-5\">";
    print_r( $_GET );
    print_r( $_POST );
    print_r( $_SESSION );
    print_r( $_FILES );
    print "</pre>";

}