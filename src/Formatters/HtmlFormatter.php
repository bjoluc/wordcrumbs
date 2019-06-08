<?php
/**
 * The WordCrumbs HtmlFormatter class
 *
 * @author bjoluc <25661029+bjoluc@users.noreply.github.com>
 * @version 1.0.0
 *
 * @license GPL
 */

namespace bjoluc\WordCrumbs\Formatters;

/**
 * A Formatter that wraps breadcrumb names in <a> tags and separates breadcrumbs
 * by a separator string. If a breadcrumb is marked as active or does not have
 * an url, a <span> tag is used instead of the <a>.
 *
 * @since 1.0.0
 */
class HtmlFormatter extends Formatter
{
    protected $_anchorClasses;

    private $__currentBreadcrumbHasLink;

    /**
     * Creates a new HtmlFormatter.
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
