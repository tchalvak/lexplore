<?php
  DEFINE('ROOT', __DIR__.'/../');
  require_once(ROOT.'core/lib_wordup.php');

  if (isset($_GET["q"])) {
    $theWord = hesc(preg_replace("/[^ \w]+/", "", strip_tags($_GET["q"])));
    $theQuery = urlencode($theWord);
  }
  
  $dictionary_section = $slang_section = $thesaurus_section = null;
  if($theQuery){
    $start_content = "<img src='img/load2.gif' />";
    $javascript = get_javascript($theQuery);
    $keys = include 'keys.php';
    $dictionary_section = dictionary_get($theQuery, $theWord, $keys['MERRIAM_WEBSTER_KEY'], false);
    $slang_section = slang_get($theQuery, false);
    $thesaurus_section = thesaurus_get($theQuery, $keys['WIKISAURUS_KEY'], false);
  } else {
    $theWord = "type a word to look it up";
}

  $content_present = null;
  if($theQuery){
    $content_present = 'content-present';
  }

  include(ROOT.'templates/main.php');

?>