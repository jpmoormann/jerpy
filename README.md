# Jerpy

## Getting Started
Jerpy is a ridiculuously simple, flat-file CMS that leverages the out-of-the-box features of PHP to produce manageable websites in just a 2 file system.

To manage Jerpy's configuration, just edit the `config.json` in the root directory.

To add pages, create new .html or .php files in the `/pages` directory, and then add a route entry to the `pageRoutes` config property.

To edit the default template, or make a new one, just manage the files under the `/themes` directory (See *Themes* section). To change the theme, set the `theme` config property to the name of the theme folder you want to use.

## Config Options
The following property options are available:
- `base` Optional String. If you want to install Jerpy to a sub-directory, you can specify the new base directory from which all the absolute paths are created
- `debug` Optional Boolean. Setting this will display all errors. This is turned on by default, and should be removed or set to false when deploying to production
- `cacheLimit` Integer. This is the number of seconds for which to cache page requests. See page caching below for more info
- `siteName` String. Sets the name of the website, which can be accessed via the $siteName global variable in templates and pages
- `theme` String. This sets which theme you want to use for the templating
- `pageRoutes` Object. This stores all the routes you want to define and the pages they will point to in the pages folder. See page routes below

## Page Routes
Each key in the `pageRoutes` object is the route by which the page will be accessed. The route should be in the following format: `/route/to/something`.

For each page route key object, the following properties are supported:
- `title` String. Title of the page
- `file` String. Name of file in pages folder
- `ogTags` Optional Object. Defines OpenGraph meta tags for proper link-sharing on social media

Example:

```
{
  "pageRoutes": {
    "/": {
      "title": "Homepage",
      "file": "home.php",
      "ogTags": {
        "title": "Welcome to our website!",
        "description": "Find out more",
        "image": "//site.com/image.png",
        "url": "//site.com/"
      }
    }
  }
}
```

The page files associated with each route entry shuold be stored in the `/pages` directory, and referenced in the config by the filename only; the directory path is prefixed automatically.

## Page Caching
Jerpy provides built-in page caching to help improve performance and lower server requests. The server will dynamically serve a page if it's client-side cache is older than the page's modified datetime, and use the cache when it's not.

In addition, a max cache time limit is set on each request to ensure that a fresh copy of a page is returned after a max amount of time. The default value is set to 18000 seconds, or 5 hours. You can adjust this via the `cacheLimit` config property.

## Themes
All themes must have at least the following in its directory:
- `index.php` This is the template for the theme
- `assets` A directory for any css, js, or other assets used in the template
- `partials` A directory used to store partial content includes, like headers, footers, and other sections

## App Variables
You can access app variables from within pages, partials, and theme templates using `$this->variable`.

### Theme, Route, and Page Variables
The path to the current theme, and the URL to its assets folder, are provided on the `theme` object, via `path` and `assets`, respectively.

The current URL, route path, and any URL parameters, are provided on the `route` object, via `url`, `path`, and `params`, respectively.

The current page's title, file path, OpenGraph tags object, and a method to render any OpenGraph tags into `<meta>` tags, are provided on the `page` object, via `title`, `file`, `ogTags`, and `renderOgTags()`, respectively.

### Full Variable Schema Reference

- `base` String
- `siteName` String
- `theme` Object
  - `path` String
  - `assets` String
- `routes` Object
- `route` Object
  - `url` String
  - `path` String
  - `params` Object
- `page` Object
  - `title` String
  - `file` String
  - `ogTags` Object
  - `parseOgTags()` Method