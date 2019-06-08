<?php
/**
 * The WordCrumbs Formatter base class
 *
 * @author bjoluc <25661029+bjoluc@users.noreply.github.com>
 * @version 1.0.0
 *
 * @license GPL
 */

namespace bjoluc\WordCrumbs\Formatters;

/**
 * TODO
 */
class Formatter
{
    protected $_separator;

    /**
     * Initialize a new Formatter.
     *
     * @param string $separator An arbitrary string that is used as a separator between breadcrumbs
     */
    public function __construct($separator = '')
    {
        $this->_separator = $separator;
    }

    /**
     * Is called before the breadcrumbs are formatted.
     *
     * @return string A string to be prepended, e.g. an opening HTML list tag
     */
    public function getPreList()
    {
        return '';
    }

    /**
     * Is called after the breadcrumbs have been formatted.
     *
     * @return string A string to be appended, e.g. a closing HTML list tag
     */
    public function getPostList()
    {
        return '';
    }

    /**
     * Is called when a breadcrumb is formatted, before its name is added.
     *
     * @param Breadcrumb $breadcrumb The breadcrumb that is being formatted.
     * @param boolean $isLast Whether the current breadcrumb is the last breadcrumb
     *
     * @return string A string to be prepended to the breadcrumb's name, e.g. an opening HTML anchor tag
     */
    public function getPreBreadcrumb($breadcrumb, $isLast)
    {
        return '';
    }

    /**
     * Is called when a breadcrumb is formatted, after its name has been added.
     *
     * @param Breadcrumb $breadcrumb The breadcrumb that is being formatted.
     * @param boolean $isLast Whether the current breadcrumb is the last breadcrumb
     *
     * @return string A string to be appended to the breadcrumb's name, e.g. a closing HTML anchor tag
     */
    public function getPostBreadcrumb($breadcrumb, $isLast)
    {
        return !$isLast ? $this->_separator : '';
    }

    /**
     * Is called to format a breadcrumb. In its default implementation returns
     * the breadcrumb's title.
     *
     * @param Breadcrumb $breadcrumb
     * @return string The string resulting from the provided Breadcrumb object
     */
    public function getBreadcrumb($breadcrumb)
    {
        return $breadcrumb->title;
    }
}
