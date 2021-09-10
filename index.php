<?php

class Page {
  public $title;
  public $file;
  public $ogTags;

  function __construct($pageObj) {
    $this->title = property_exists($pageObj,'title') ? '- '.$pageObj->title : '';
    $this->file = 'pages/'.$pageObj->file;
    $this->ogTags = property_exists($pageObj,'ogTags') ? $pageObj->ogTags : null;
  }

  function renderOgTags() {
    if(!$this->ogTags) return '';
    $tags = !property_exists($this->ogTags,'type') ? ['<meta property="og:type" content="website" />'] : [];
    foreach(get_object_vars($this->ogTags) as $k => $v) {
      $tags[] = '<meta property="og:'.$k.'" content="'.$v.'" />';
    }
    return join("\r\n", $tags);
  }
}

class PageNotFound extends Page {
  public $title = 'Page Not Found';
  public $file = 'pages/404.php';
  function __construct() {}
}

class Theme {
  public $template;
  public $path;
  public $assets;

  function __construct($theme,$base = null) {
    $this->path = 'themes/'.$theme;
    $this->assets = $base.'/'.$this->path.'/assets';
    $this->template = $this->path.'/index.php';
  }
}

class Route {
  public $url;
  public $path;
  public $params;

  function __construct($base) {
    $this->url = str_replace($base,'',$_SERVER['REQUEST_URI']);
    $this->parseRoute();
  }

  function parseRoute() {
    $parts = explode('?',$this->url);
    $this->path = $parts[0];
    if(array_key_exists(1,$parts)) $this->parseParams($parts[1]);
  }

  function parseParams($params) {
    $this->params = (object)[];
    foreach(explode('&',$params) as $p) {
      $k = explode('=',$p)[0];
      $v = explode('=',$p)[1];
      $this->params->$k = $v;
    }
  }
}

class App {
  protected $config;
  protected $cacheLimit;
  public $base;
  public $siteName;
  public $theme;
  public $routes;
  public $route;
  public $page;

  function __construct() {
    $this->config = json_decode(file_get_contents('config.json'));    
    $this->cacheLimit = $this->getConfig('cacheLimit');
    $this->base = $this->getConfig('base');
    $this->siteName = $this->getConfig('siteName');
    $this->theme = new Theme($this->getConfig('theme'),$this->base);
    $this->routes = $this->getConfig('pageRoutes');
    $this->route = new Route($this->base);
    $this->page = new PageNotFound();
  }

  function getConfig($key) {
    return property_exists($this->config,$key) ? $this->config->$key : null;
  }

  function checkRoute() {    
    if(property_exists($this->routes,$this->route->path)) {
      $page = new Page($this->routes->{$this->route->path});
      if(file_exists($page->file)) return true;
      return false;
    }
    return false;
  }

  function run() {
    if($this->getConfig('debug')) {
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);
    }
    if($this->checkRoute()) $this->page = new Page($this->routes->{$this->route->path});
    ob_start();
    $modTime = filemtime($this->page->file);
    header('Cache-Control: max-age='.$this->cacheLimit.',public');
    header("Last-Modified: ".gmdate('D, d M Y H:i:s', $modTime)." GMT");
    header('Expires: '.gmdate('D, d M Y H:i:s', $modTime + ($this->cacheLimit)).' GMT');
    header('Pragma: cache');
    if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $modTime) { 
      header('HTTP/1.1 304 Not Modified');
      exit;
    }
    include $this->theme->template;
    ob_get_flush();
  }
}

(new App())->run();