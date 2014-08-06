<html>
 <head>
  <meta charset="utf-8"> 
  <title>WordUp</title>
  <link rel="stylesheet" type="text/css" href="./css/styles.css">
  <?php echo $javascript; ?>
 </head>

 <body>

  <div id="page-wrap">

    <div id="search-area">
      <div class="left-display">
        <?php echo $theWord; ?>
      </div>
      <div class="right-display">
        <form action="index.php" method="get">
          <input type="text" name="q" class="textbox" />
          <a href='info.php'>
            <img src='img/icon_info_1.svg' style='height: 32px; width: 32px;' />
          </a>
        </form>
      </div>
    </div>

    <div id="display-area" class='<?php echo $content_present;?>'>
      <div id='dictionary' class='definition-box'><?php echo $start_content; ?></div>
      <div id='slang' class='definition-box'><?php echo $start_content; ?></div>
      <div id='thesaurus' class='definition-box'><?php echo $start_content; ?></div>
   </div>
   
  </div>

 </body>
</html>