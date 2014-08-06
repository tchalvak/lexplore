<?php
  DEFINE('ROOT', __DIR__.'/../');
  require_once(ROOT.'core/lib_wordup.php');

  if (isset($_GET["q"])) {
    $theWord = hesc(preg_replace("/[^ \w]+/", "", strip_tags($_GET["q"])));
    $theQuery = urlencode($theWord);
  }
    
  $dictionary_section = $slang_section = $thesaurus_section = null;
  if($theQuery){
    $keys = include 'keys.php';
    $dictionary_section = dictionary_get($theQuery, $theWord, $keys['MERRIAM_WEBSTER_KEY'], true);
    $slang_section = slang_get($theQuery, true);
    $thesaurus_section = thesaurus_get($theQuery, $keys['WIKISAURUS_KEY'], true);

    echo "{\"word\":\"".$theWord."\",".
       $dictionary_section.",".
       $slang_section.",".
       $thesaurus_section.
       "}";
    }
  
?>