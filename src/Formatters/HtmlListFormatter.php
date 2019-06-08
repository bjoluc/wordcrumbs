<?php
/**
 * The WordCrumbs HtmlListFormatter class
 *
 * @author bjoluc <25661029+bjoluc@users.noreply.github.com>
 * @version 1.0.0
 *
 * @license GPL
 */

namespace bjoluc\WordCrumbs\Formatters;

/**
 * A Formatter that produces an HTML list whose entries are breadcrumb names,
 * wrapped in <a> tags. If a breadcrumb is marked as active or does not have an
 * url, a <span> tag is used instead of the <a>.
 *
 * @since 1.0.0
 */
class HtmlListFormatter extends HtmlFormatter
{
    protected $_listClasses;

    protected $_listEntryClasses;

    /**
     * Creates a new HtmlListFormatter.
     *
     * @param string[] $anchorClasses (optional) An array of css classes that
     *        are added to the HTML anchor (or span) tags that wrap breadcrumbs
     * @param string[] $listClasses (optional) An array of css classes that are
     *        added to the wrapping <li> element
     * @param string[] $listEntryClasses (optional) An array of css classes that
     *        are added to the <li> tags that wrap breadcrumbs
     */
    public function __construct($anchorClasses = [], $listClasses = [], $listEntryClasses = [])
    {
        parent::__construct('', $anchorClasses);
        $this->_listClasses = implode(' ', $listClasses);
        $this->_listEntryClasses = implode(' ', $listEntryClasses);
    }

    public function getPreList()
    {
        $tag = '<ul';
        if ($this->_listClasses !== '') {
            $tag .= ' class="' . $this->_listClasses . '"';
        }
        return $tag . '>' . parent::getPreList();
    }

    public function getPostList()
    {
        return parent::getPostList() . '</ul>';
    }

    public function getPreBreadcrumb($breadcrumb, $isLast)
    {
        $tag = '<li';
        if ($this->_listEntryClasses !== '') {
            $tag .= ' class="' . $this->_listEntryClasses . '"';
        }
        return $tag . '>' . parent::getPreBreadcrumb($breadcrumb, $isLast);
    }

    public function getPostBreadcrumb($breadcrumb, $isLast)
    {
        return parent::getPostBreadcrumb($breadcrumb, $isLast) . '</li>';
    }
}
