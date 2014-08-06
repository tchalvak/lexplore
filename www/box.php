<?php
  DEFINE('ROOT', __DIR__.'/../');
  require_once(ROOT.'core/lib_wordup.php');

  if (isset($_GET["q"])) {
    $theWord = hesc(preg_replace('/\PL/u', '', strip_tags($_GET["q"])));
    $theQuery = urlencode($theWord);
  }

  $box_content = null;
  $keys = include 'keys.php';
  switch ($_GET["type"]) {
    case "dictionary":
      $box_content = dictionary_get($theQuery, $theWord, $keys['MERRIAM_WEBSTER_KEY'], false);
      break;
    case "slang":
      $box_content = slang_get($theQuery, false);
      break;
    case "thesaurus":
      $box_content = thesaurus_get($theQuery, $keys['WIKISAURUS_KEY'], false);
      break;
  }
  include(ROOT.'templates/box.php');  
?>