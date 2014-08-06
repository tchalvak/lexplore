$(document).ready(function(){
  $("#dictionary").load("http://mindsoon.com/wordup/www/box.php?type=dictionary&q="+word);
  $("#slang").load("http://mindsoon.com/wordup/www/box.php?type=slang&q="+word);
  $("#thesaurus").load("http://mindsoon.com/wordup/www/box.php?type=thesaurus&q="+word);
});