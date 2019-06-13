<?php
/**
 * The WordCrumbs PlainFormatter class
 *
 * @author bjoluc <25661029+bjoluc@users.noreply.github.com>
 * @version 1.0.0
 *
 * @license GPL-3.0-or-later
 */

namespace bjoluc\WordCrumbs\Formatters;

/**
 * A formatter that prints breadcrumbs in plain text with an optional separator.
 */
class PlainFormatter extends BaseFormatter
{
    protected $_separator;

    /**
     * Initialize a new PlainFormatter.
     *
     * @param string $separator An arbitrary string that is used as a separator between breadcrumbs
     */
    public function __construct($separator = '')
    {
        $this->_separator = $separator;
    }

    public function getPostBreadcrumb($breadcrumb, $isLast)
    {
        return !$isLast ? $this->_separator : '';
    }
}
