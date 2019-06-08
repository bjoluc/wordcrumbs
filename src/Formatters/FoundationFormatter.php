<?php
/**
 * A Formatter implementation for Zurb Foundation breadcrumbs
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
class FoundationFormatter extends HtmlListFormatter
{
    protected $_separator;

    protected $_navClasses;

    /**
     * @inheritDoc
     * @param string$navClasses (optional) An array of css classes that are
     *        added to the wrapping <nav> tag
     */
    public function __construct($navClasses = [], $anchorClasses = [], $listClasses = [], $listEntryClasses = [])
    {
        parent::__construct($anchorClasses, ['breadcrumbs'] + $listClasses, $listEntryClasses);
        $this->_navClasses = implode(' ', $navClasses);
    }

    public function getPreList()
    {
        $tag = '<nav aria-label="You are here:" role="navigation"';
        if ($this->_navClasses !== '') {
            $tag .= ' class="' . $this->_navClasses . '"';
        }
        return $tag . '>' . parent::getPreList();
    }

    public function getPostList()
    {
        return parent::getPostList() . '</nav>';
    }

    public function getBreadcrumb($breadcrumb)
    {
        return ($breadcrumb->active ? '<span class="show-for-sr">Aktive Seite: </span>' : '') . $breadcrumb->title;
    }
}
