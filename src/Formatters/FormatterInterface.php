<?php
/**
 * The WordCrumbs FormatterInterface
 *
 * @author bjoluc <25661029+bjoluc@users.noreply.github.com>
 * @version 1.0.0
 *
 * @license GPL-3.0-or-later
 */

namespace bjoluc\WordCrumbs\Formatters;

/**
 * Specifies the methods of a Formatter. Formatters are used by the WordCrumb
 * `format()` method to convert a list of Breadcrumb objects into a string.
 */
interface FormatterInterface
{
    /**
     * Is called by the WordCrumbs class before using the formatter.
     *
     * @param Symfony\Component\Translation\Translator $translator The Symfony
     *        Translator instance used for translations
     * @return void
     */
    public function setTranslator($translator);

    /**
     * Is called before the breadcrumbs are formatted.
     *
     * @return string A string to be prepended, e.g. an opening HTML list tag
     */
    public function getPre();

    /**
     * Is called after the breadcrumbs have been formatted.
     *
     * @return string A string to be appended, e.g. a closing HTML list tag
     */
    public function getPost();

    /**
     * Is called when a breadcrumb is formatted, before its name is added.
     *
     * @param Breadcrumb $breadcrumb The breadcrumb that is being formatted.
     * @param boolean $isLast Whether the current breadcrumb is the last breadcrumb
     *
     * @return string A string to be prepended to the breadcrumb's name, e.g. an opening HTML anchor tag
     */
    public function getPreBreadcrumb($breadcrumb, $isLast);

    /**
     * Is called when a breadcrumb is formatted, after its name has been added.
     *
     * @param Breadcrumb $breadcrumb The breadcrumb that is being formatted.
     * @param boolean $isLast Whether the current breadcrumb is the last breadcrumb
     *
     * @return string A string to be appended to the breadcrumb's name, e.g. a closing HTML anchor tag
     */
    public function getPostBreadcrumb($breadcrumb, $isLast);

    /**
     * Is called to format a breadcrumb.
     *
     * @param Breadcrumb $breadcrumb The Breadcrumb to be formatted
     * @return string The string resulting from the provided Breadcrumb object
     */
    public function getBreadcrumb($breadcrumb);
}
