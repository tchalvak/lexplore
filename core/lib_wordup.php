<?php
// Collection of the php functions for the wordup dictionary app.

// Pull content from dictionary API and prepare for website (HTML) or internal API (JSON)
function dictionary_get ($theQuery,$theWord,$theKey,$isAPI) {
    $name = "merriam-webster";
    $url = "http://www.dictionaryapi.com/api/v1/references/collegiate/xml/".$theQuery."?key=".$theKey;
    $link = "http://www.merriam-webster.com/dictionary/".$theQuery;
    $obj = simplexml_load_string(file_get_contents($url));
    if ($isAPI) {
      return dictionary_json($name,$link,$obj,$theWord);
    } else {
      return dictionary_html($name,$link,$obj,$theWord);
    }
}

// Pull content from slang API and prepare for website (HTML) or internal API (JSON)
function slang_get ($theQuery,$isAPI) {
    $name = "urban dictionary";
    $url = "http://api.urbandictionary.com/v0/define?term=".$theQuery;
    $link = "http://www.urbandictionary.com/define.php?term=".$theQuery;
    $obj = json_decode( clear_formatting( file_get_contents($url) ) );
    if ($isAPI) {
      return slang_json($name,$link,$obj);
    } else {
      return slang_html($name,$link,$obj);
    }
}

// Pull content from thesaurus API and prepare for website (HTML) or internal API (JSON)
function thesaurus_get ($theQuery,$theKey,$isAPI) {
    $name = "wikisaurus";
    $url = "http://thesaurus.altervista.org/thesaurus/v1?word=".$theQuery."&output=json&language=en_US&key=".$theKey;
    $link = "http://en.wiktionary.org/wiki/".$theQuery."#Synonyms";
    $obj = json_decode(file_get_contents($url));
    if ($isAPI) {
      return thesaurus_json($name,$link,$obj);
    } else {
      return thesaurus_html($name,$link,$obj);
    }
}

// Convert stock input from dictionary API into customized JSON.
function dictionary_json ($name,$link,$obj,$theWord) {
    $json = "\"dictionary\":{\"name\":\"".$name."\",\"link\":\"".$link."\",\"entries\":[";  
    foreach($obj->entry as $thisEntry) {
      if ($thisEntry->ew == $theWord) {
        foreach($thisEntry->def as $def) {
          $json .= "{\"firstUsage\":\"".hesc($thisEntry->def->date)."\",\"partOfSpeech\":\"".hesc($thisEntry->fl)."\",\"definitions\":[";          
          foreach($def->dt as $dt) {
            $definition = clear_formatting( ltrim ( strip_tags($dt->asXML(),"<sx>"), ': ') );
            $json .= "{\"def\":\"".$definition."\"},";
          }
          $json = rtrim($json, ',')."]";
        }
        $json .="},";
      }
    }
   return rtrim($json, ',')."]}";
}

// Convert stock input from slang API into customized JSON.
function slang_json ($name,$link,$obj) {
    if (isset($obj->list[0]->definition)) {
      $definition = $obj->list[0]->definition;
      $example = $obj->list[0]->example;
    }
    return "\"slang\":{\"name\":\"".$name."\",\"link\":\"".$link."\",\"definition\":\"".$definition."\",\"example\":\"".$example."\"}";
}

// Convert stock input from thesaurus API into customized JSON.
function thesaurus_json ($name,$link,$obj) {
    $json = "\"thesaurus\":{\"name\":\"".$name."\",\"link\":\"".$link."\",\"entries\":[";  
    if (isset($obj->response)) {
      foreach ($obj->response as $response) {
        $json .= "{\"partOfSpeech\":\"".$response->list->{'category'}."\",\"synonyms\":\"".$response->list->{'synonyms'}."\"},";
        }
      }
    return clear_formatting( rtrim($json,',') )."]}";
}

// Convert customized dictionary JSON into HTML for output.
function dictionary_html($name,$link,$obj,$theWord) {
    $html = "";
    $json = json_decode( "{".dictionary_json($name,$link,$obj,$theWord)."}" );
    if (isset($json->dictionary->entries)) {
      foreach($json->dictionary->entries as $entry) {
        $html .= "<p class='gray definition-tag'>".hesc($entry->firstUsage)."<span class=dates> (".hesc($entry->partOfSpeech).")</span></p>";
        foreach($entry->definitions as $definition) {
          $html .= "<p class='entry'>".strip_tags($definition->def)."</p>";
        }
      }
    }
    return html_tagline($name,$link,$html);
}

// Convert customized slang JSON into HTML for output.
function slang_html($name,$link,$obj) {
    $html = "";
    $json = "{".slang_json($name,$link,$obj)."}";
    $json = json_decode( $json );
    if (isset($json->slang->definition)) {
      $html = "<p class='entry'>".$json->slang->definition."</p>".
              "<p class='entry'><i>".$json->slang->example."</i></p>";
    }
    return html_tagline($name,$link,$html);
}

// Convert customized thesaurus JSON into HTML for output.
function thesaurus_html($name,$link,$obj) {
    $html = "";
    $json = json_decode( "{".thesaurus_json($name,$link,$obj)."}" );
    if (isset($json->thesaurus->entries)) {
      foreach($json->thesaurus->entries as $entry) {
        $html .= "<p class='entry'><span class='gray dates'>".$entry->partOfSpeech."</span> ".$entry->synonyms."</p>";
        }
      }
    return html_tagline($name,$link,$html);
}

// Show reference to jquery script and internal 
function get_javascript($theQuery) {
    return "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>".
           "<script type='text/javascript'>".
           "var word = '".$theQuery."';".
           "</script>".
           "<script type='text/javascript' src='./scripts/wordup.js'></script>";
}

// Replace various occurences of substrings within an input string.
function clear_formatting ($string) {
    $bad  = array("\\r\\n", "\\\"", " (similar term)", " (related term)",  "|",  "</sx> <sx>", " :");
    $good = array(" ",      "'",    "",                "",                 ", ", ", ",         ", ");
    return strip_tags( str_replace($bad,$good,$string) );
}

// Add HTML tagline to bottom of each entry with a link to its webpage.
function html_tagline ($name,$link,$html) {
    if ($html == "") {
      return "no results at ".$name;
    } else {
      return $html."<p class='definition-tag'>( <a href='".hesc($link)."' target='_blank'>more at ".hesc($name)."</a> )</p>";
    }
}

// Clean out possibly dirty input and return as a string.
function hesc($dirty){
  return htmlentities($dirty, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
}

?>