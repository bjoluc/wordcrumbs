# WordCrumbs
A simple PHP package to add breadcrumbs on any WordPress site

Ever needed to add customized breadcrumbs to your WordPress theme without annoying your users with a separate plugin? Now you can! WordCrumbs is a customizable, easily extensible PHP package to automatically generate breadcrumbs on WordPress sites. It comes with a set of formatters that can be used or extended to control WordCrumb's HTML output.

## I want this, what do I have to do?

If you do not use composer, [install](https://getcomposer.org/) it first. With composer installed, navigate to your theme's directory and run 
``composer require bjoluc/wordcrumbs``.

If your theme is not already using the composer autoloader, require it in your `functions.php`:
```require __DIR__ . '/vendor/autoload.php';```

After that, for example in your theme's `header.php`, you can use WordCrumbs like this:
```php
<?php
    use bjoluc\WordCrumbs\WordCrumbs;
    use bjoluc\WordCrumbs\Formatters\HtmlFormatter;

    $wordcrumbs = new WordCrumbs();
    $wordcrumbs->detect();
    $formatter = new HtmlFormatter(' &raquo; ');
    print $wordcrumbs->format($formatter);
?>
```

## Customization
### Behavior

By default, WordCrumbs displays breadcrumbs on posts, pages, category archives, tag archives, date archives, attachments, search results, and the 404 error page. You can disable any of these using the following methods before calling `detect()`:
* `$wordcrumbs->disablePosts();`
* `$wordcrumbs->disablePages();`
* `$wordcrumbs->disableCategories();`
* `$wordcrumbs->disableTags();`
* `$wordcrumbs->disableDates();`
* `$wordcrumbs->disableAttachments();`
* `$wordcrumbs->disableSearch();`
* `$wordcrumbs->disableError404();`

Breadcrumbs for custom taxonomies and custom post types are disabled by default. They can be enabled using the following methods:
* `$wordcrumbs->enableCustomTaxonomies()`
* `$wordcrumbs->enableCustomPostTypes()`

Both of these also accept an array of taxonomy resp. post type names for which to enable breadcrumbs.

If you have a page for a custom taxonomy where you list the taxonomies terms, you can make the taxonomy breadcrumb link to it by using `enableTermListPages()`. For example, if you have a page at `/colors` where terms of the taxonomy "color" are listed and you call `enableTermListPages()` or, more specifically, `enableTermListPages(['color'])`, color terms will have the breadcrumb "Colors" link to `/colors`.

### Formatting

You can customize the HTML (or plain text) output in several ways. First, you can switch between existing formatters. Those are:
* [PlainFormatter](src/Formatters/PlainFormatter.php)
* [HtmlFormatter](src/Formatters/HtmlFormatter.php)
* [HtmlListFormatter](src/Formatters/HtmlListFormatter.php)
* [FoundationFormatter](src/Formatters/FoundationFormatter.php) (for [Zurb Foundation](https://foundation.zurb.com/))

Second, all of these formatters support optional constructor arguments to modify their output (see the PHPDoc comments for details).

Third, you can extend any of these formatters (including the abstract [BaseFormatter](src/Formatters/BaseFormatter.php)) class or implement the [FormatterInterface](src/Formatters/FormatterInterface.php) yourself.

### Locales

Locales are stored as yaml files in the [locales](locales) directory. The preferred locale can be passed as a string to the WordCrumbs constructor (e.g. `new WordCrumbs('de_DE')`). Otherwise, the WordPress function `get_locale()` is used to set the locale. If the respective locale file does not exist en_US is used as a fallback.
Adding locales is as easy as copying [en_US.yaml](locales/en_US.yaml) and modifying the strings accordingly. If you add a locale please feel free to create a merge request or open an issue providing the yaml file contents. I will be glad to accept your request or add the file for you!

### In-depth modifications

Depending on the page, the `detect()` method invokes different protected methods prefixed with `_process`. If, for example, you would like to modify the way breadcrumbs are created on attachment pages, you could simply subclass `WordCrumbs` and override `_processAttachment()` accordingly. Have fun!