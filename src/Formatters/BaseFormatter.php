<?php
/**
 * The WordCrumbs BaseFormatter FormatterInterface implementation
 *
 * @author bjoluc <25661029+bjoluc@users.noreply.github.com>
 * @version 1.0.0
 *
 * @license GPL-3.0-or-later
 */

namespace bjoluc\WordCrumbs\Formatters;

/**
 * A base class for formatters that implements the FormatterInterface.
 */
class BaseFormatter implements FormatterInterface
{
    /**
     * The Symfony Translator instance used for translations
     *
     * @var Symfony\Component\Translation\Translator
     */
    protected $_translator;

    public function setTranslator($translator)
    {
        $this->_translator = $translator;
    }

    public function getPre()
    {
        return '';
    }

    public function getPost()
    {
        return '';
    }

    public function getPreBreadcrumb($breadcrumb, $isLast)
    {
        return '';
    }

    public function getPostBreadcrumb($breadcrumb, $isLast)
    {
        return '';
    }

    /**
     * Is called to format a breadcrumb.
     *
     * @param Breadcrumb $breadcrumb The Breadcrumb to be formatted
     * @return string The Breadcrumb's title
     */
    public function getBreadcrumb($breadcrumb)
    {
        return $breadcrumb->title;
    }
}
