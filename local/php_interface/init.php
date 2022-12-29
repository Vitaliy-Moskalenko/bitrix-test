<?php

define("IB_CALLBACK", 1);
define("IB_REQUEST", 13);
define("FEEDBACK_HL_BLOCK", 1);
define("SITE_ID", 's1');



function dump($data, $file = false, $filename = "debug.log", $rewrite = false) {
    $backtrace = debug_backtrace();
    $cp = "{$backtrace[0]["file"]}, {$backtrace[0]["line"]}";

    if($file && $fp = fopen("{$_SERVER["DOCUMENT_ROOT"]}/local/logs/{$filename}", $rewrite ? "w" : "a")):
        fwrite($fp, "<pre>" . print_r(array(
                "date"=>date("d.m.Y H:i:s"),
                "file"=>$cp,
                "data"=>$data
            ), true) . "</pre>\n");
        fclose($fp);
    else:
        global $USER;
        if(!$_REQUEST["debug"] && !$USER->isAdmin())
            return;

        $jsonString = json_encode(
            unserialize(
                str_replace(
                    array('NAN;','INF;'), '0;', serialize($data)
                )
            ), JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE
        );
        if(!$jsonString)
            $jsonString = json_encode(json_last_error_msg());

        echo "<script>\n
            \tconsole.log(\"{$cp}\");\n
            \tconsole.log({$jsonString});\n
        </script>\n";
    endif;
}