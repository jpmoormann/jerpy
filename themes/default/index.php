<html>
  <head>
    <title>Website <?=$this->page->title?></title>
    <link rel="icon" href="#"/>
    <link rel="stylesheet" href="<?=$this->theme->assets?>/styles.css"/>
    <?=$this->page->renderOgTags()?>
  </head>
  <body>
    <header><?php include($this->theme->path.'/partials/header.php'); ?></header>
    <main><?php include($this->page->file); ?></main>
  </body>
</html>
