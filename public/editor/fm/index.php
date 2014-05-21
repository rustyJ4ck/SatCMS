<?php

/*
 * https://github.com/Studio-42/elFinder
 */

require "php/Authorize.php";

if (!elFinderIsAuthed()) {
   die(<<<MSG
<html>
<head>
<link href="/templates/default/default.css" rel="stylesheet" type="text/css" />
<style> html,body{padding:0;margin:0;height:100%;width:100%;}
h1 {position:absolute;top:45%;text-align:center;border:0px solid red;left:50%;width:300px;margin-left:-150px;}
</style>
</head>
<body><h1><a href="/">You shall not pass!!!</a></h1>
</body>
</html>
MSG
);
}                               

$buffer = file_get_contents("_index.html");

$script = '';
$init = '';

/**
 * Tinymce plugin
 */
if (@$_REQUEST['from'] == 'tinymce') {

    $script = <<<'SCRIPT'
        var FileBrowserDialogue = {
            init: function() {
              // Here goes your code for setting your custom things onLoad.
            },
            mySubmit: function (URL) {
              // pass selected file path to TinyMCE
              parent.tinymce.activeEditor.windowManager.getParams().setUrl(URL);

              // close popup window
              parent.tinymce.activeEditor.windowManager.close();
            }
        }
SCRIPT;


    $init = <<<'SCRIPT'
        getFileCallback: function(file) {
            // actually file.url - doesnt work for me, but file does. (elfinder 2.0-rc1)
            FileBrowserDialogue.mySubmit(file); // pass selected file path to TinyMCE
        }
SCRIPT;

}

$buffer = str_replace(
    array('%script%', '%init%'),
    array($script, $init),
    $buffer
);

/**
 * Output
 */
if (@$_REQUEST['embed']) {

    echo '<iframe src="/editor/fm/index.php"
                  style="width:100%;height:620px;border:0"
                  frameborder="0"></iframe>';

} else {

    echo $buffer;

}
