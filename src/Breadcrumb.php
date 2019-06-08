<?php
/**
 * The WordCrumbs Breadcrumb class
 *
 * @author bjoluc <25661029+bjoluc@users.noreply.github.com>
 * @version 1.0.0
 *
 * @license GPL
 */

namespace bjoluc\WordCrumbs;

/**
 * A class for representing single breadcrumb entries
 */
class Breadcrumb
{

    /**
     * The breadcrumb's title
     *
     * @var string
     */
    public $title;

    /**
     * The URL belonging to the breadcrumb
     *
     * @var string
     */
    public $url;

    /**
     * If true, marks the breadcrumb as belonging to the current page.
     *
     * @var boolean
     */
    public $active = false;

    /**
     * Creates a new Breadcrumb object.
     *
     * @param string $title The breadcrumb's title
     * @param string $url (optional) The URL belonging to the breadcrumb
     */
    public function __construct($title, $url = '')
    {
        $this->title = $title;
        $this->url = $url;
    }
}
