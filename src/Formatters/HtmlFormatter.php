<?php
/**
 * The WordCrumbs HtmlFormatter class
 *
 * @author bjoluc <25661029+bjoluc@users.noreply.github.com>
 * @version 1.0.0
 *
 * @license GPL-3.0-or-later
 */

namespace bjoluc\WordCrumbs\Formatters;

/**
 * A Formatter that wraps breadcrumb names in <a> tags and optionally separates
 * breadcrumbs by a separator string. If a breadcrumb is marked as active or
 * does not have an url, <span> is used instead of <a>.
 *
 * @since 1.0.0
 */
class HtmlFormatter extends PlainFormatter
{
    protected $_anchorClasses;

    private $__currentBreadcrumbHasLink;

    /**
     * Initialize a new HtmlFormatter.
     *
     * @param string $separator (optional) An arbitrary string that is used as a
     *        separator between breadcrumbs
     * @param string[] $anchorClasses (optional) An array of css classes that
     *        are added to the HTML anchor (or span) tags that wrap breadcrumbs
     */
    public function __construct($separator = '', $anchorClasses = [])
    {
        parent::__construct($separator);
        $this->_anchorClasses = implode(' ', $anchorClasses);
    }

    public function getPreBreadcrumb($breadcrumb, $isLast)
    {
        $hasLink = !($breadcrumb->url == '' || $breadcrumb->active);
        $this->__currentBreadcrumbHasLink = $hasLink;
        if ($hasLink) {
            $tag = '<a href="' . $breadcrumb->url . '"';
        } else {
            $tag = '<span';
        }

        if ($this->_anchorClasses !== '') {
            $tag .= ' class="' . $this->_anchorClasses . '"';
        }

        return $tag . '>';
    }

    public function getPostBreadcrumb($breadcrumb, $isLast)
    {
        return ($this->__currentBreadcrumbHasLink ? '</a>' : '</span>') . (!$isLast ? $this->_separator : '');
    }
}
