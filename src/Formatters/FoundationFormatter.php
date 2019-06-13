<?php
/**
 * The WordCrumbs FoundationFormatter class
 *
 * @author bjoluc <25661029+bjoluc@users.noreply.github.com>
 * @version 1.0.0
 *
 * @license GPL-3.0-or-later
 */

namespace bjoluc\WordCrumbs\Formatters;

/**
 * A Formatter implementation for the Zurb Foundation breadcrumbs component
 */
class FoundationFormatter extends HtmlListFormatter
{
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

    public function getPre()
    {
        $youAreHere = $this->_translator->trans('wordcrumbs.screen_reader.pre_breadcrumbs');
        $tag = '<nav aria-label="' . $youAreHere . '" role="navigation"';
        if ($this->_navClasses !== '') {
            $tag .= ' class="' . $this->_navClasses . '"';
        }
        return $tag . '>' . parent::getPre();
    }

    public function getPost()
    {
        return parent::getPost() . '</nav>';
    }

    public function getBreadcrumb($breadcrumb)
    {
        if ($breadcrumb->active) {
            return '<span class="show-for-sr">' .
            $this->_translator->trans('wordcrumbs.screen_reader.current_page') .
            '</span>' . $breadcrumb->title;
        } else {
            return $breadcrumb->title;
        }
    }
}
