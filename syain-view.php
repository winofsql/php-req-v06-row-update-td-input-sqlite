<!DOCTYPE html>
<html>

<head>
<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
<meta charset="utf-8">
<title><?= $title ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.js"></script>

<style>
html,body,form {
    height: 100%;
}
#head {
    display: block;
    margin: auto;
    width: 100%;
    height: 160px!important;
    height: 100%;
}
#content {
    padding: 4px 16px;
    display: block;
    margin: auto;
    width: calc( 100% - 3px );
    height: calc( 100% - 160px - 2px );
    border: solid 2px #c0c0c0;
    overflow: scroll;
}

td,th {
    cursor: default!important;
}
th {
    white-space: pre;
}

#tbl {
    user-select: none;
}

.w100 {
    width: 100px;
}
.w300 {
    width: 300px;
}

.pre {
    white-space: pre;
}

.folder {
    float: right;
}
</style>
<script>
$(function(){

    $("form").on("submit", function(){

        if ( !confirm("更新してもよろしいですか?") ) {
            event.preventDefault();
            return;
        }

    });

    $("input[name='update']").on( "click", function(){

        var update = "";

        $("#data .row_data").each(function(i){

            var cols = $(this).find("td");
            // フリガナ
            var td = cols.eq(2);

            if ( td.data("change") == "yes" ) {

                update += "update 社員マスタ set ";
                update += " フリガナ = '" + td.text() + "'";
                update += " where 社員コード = '" + cols.eq(0).text() + "' ;";

            }

        });

        // 更新 SQL をテキストエリアにセット
        $("#sql").val(update);

    });


    $("#action_save").on( "click", function(){

        // CSV テキスト用変数
        var csv = "";

        // データの行数
        var cnt = 0;
        $("table")
            // 行単位で処理
            .find("tr").each( function(){

            // TH の最初の一行は処理しない )
            if ( cnt > 0 ) {

                // 行内の TD を全て処理
                $(this).find("td").each(function( col_cnt ){
                    // 先頭列以外はカンマを付加
                    if ( col_cnt != 0 ) {
                        csv += ",";
                    }

                    if ( col_cnt == 0 ) {
                    // Excel で文字列をそのまま取り込めるように( 例. 0001 を文字列として扱う )
                        csv += "=\"" + $(this).text() + "\"";
                    }
                    else {
                        if ( col_cnt == 5 ) {
                            csv += "\"" + $(this).find("input[type=text]").val() + "\"";
                        }
                        else {
                            csv += "\"" + $(this).text() + "\"";
                        }
                    }
                });
                // 行の最後に改行
                csv += "\n";
            }

            // 行数のカウント
            cnt++;

        });

        // UTF-8 の CSV を化けずに Excel で開く為
        saveAs(
            new Blob(
                [new Uint8Array([0xEF, 0xBB, 0xBF]),csv]
                , {type: "text/csv;charset=" + document.characterSet}
            )
            , "syain.csv"
        );

    });

    $("#data .row_data").each(function(i){

        // フリガナ
        var td = $(this).find("td").eq(2);

        // フリガナ
        td.css( 
            { "text-decoration":"underline", "background-color" : "pink" }
        );

        // 入力内容
        var text = td.text();
        // 入力内容を保存
        td.data("text", text );

        // フリガナ
        td.on("click", function(){
            if ( $(this).data("input") != "use" ) {
                // 入力切替えフラグ
                $(this).data("input", "use" );

                // 入力内容
                var text_now = $(this).text();

                // TD の表示を消去
                $(this).text("");

                // 入力を追加
                $("<input>")
                        .appendTo($(this))  // TD に追加
                        .val(text_now)      // テキストセット
                        .focus()            // フォーカスセット
                        // focusout で元に戻す
                        .on( "focusout", function(){

                            // 現在の入力
                            var text = $(this).val();

                            // 親 TD
                            var td = $(this).parent();

                            // データが変更されていたら『変更フラグ』をセット
                            if ( text != td.data("text") ) {
                                td.data("change", "yes");
                                td.css("background-color","silver");
                            }
                            else {
                                td.data("change", "");
                                td.css("background-color","pink");
                            }

                            // TD に値を戻して 切替えフラグを消去
                            td
                                .append(text)
                                .data("input", "" );

                            // INPUT を削除
                            $(this).remove();

                        });
            }

        });

    });

});
</Script>
</head>

<body>
<form method="post">
    <div id="head">
        <h3 class="alert alert-primary">
            <?= $title ?>
            <input id="action_save" type="button" value="CSV保存" class="btn btn-primary ms-3">
            <a href="." class="btn btn-secondary btn-sm folder me-4">フォルダ</a>
        </h3>
        <input type="submit" name="update" value="更新" class="ms-4 btn btn-primary">
    </div>
    <div id="content">

            <div class="table-responsive">
                <table id="data" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="w100">社員コード</th>
                            <th>氏名</th>
                            <th class="w300">フリガナ</th>
                            <th>所属</th>
                            <th>性別</th>
                            <th>給与</th>
                            <th class="text-end">手当</th>
                            <th>管理者</th>
                            <th>生年月日</th>
                        </tr>
                    </thead>
                    <tbody id="tbl">
                        <?= $html ?>
                    </tbody>
                </table>
            </div>
    </div>
    <textarea id="sql" name="sql" class='d-none'></textarea>
</form>
</body>
</html>