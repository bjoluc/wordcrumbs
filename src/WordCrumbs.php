<?php
/**
 * The main WordCrumbs class
 *
 * @author bjoluc <25661029+bjoluc@users.noreply.github.com>
 * @version 1.0.0
 *
 * @license GPL
 *
 *
 * TODO for publishing:
 *      * Enable or disable custom taxonomies
 *      * Enable or disable custom post types
 *      * Switch to english and include german translation
 */

namespace bjoluc\WordCrumbs;

class WordCrumbs
{
    private $__homeName;

    private $__breadcrumbs;

    /**
     * Creates a new WordCrumbs object.
     *
     * @param boolean $detectOnConstruction (optional) Whether to call
     *        {@link detect} on construction. Defaults to `false`.
     * @param string $homeName (optional) The name of the "Home" breadcrumb.
     *        Defaults to 'Home'.
     */
    public function __construct($detectOnConstruction = false, $homeName = 'Home')
    {
        $this->__homeName = $homeName;
        $this->__breadcrumbs = [];

        if ($detectOnConstruction) {
            $this->detect();
        }
    }

    /**
     * Set the name of the "Home" breadcrumb.
     *
     * @param string $homeName The name of the "Home" breadcrumb
     * @return void
     */
    public function setHomeName($homeName)
    {
        $this->__homeName = $homeName;
    }

    /**
     * Uses WordPress builtin functions to detect the page hierarchy and add
     * breadcrumbs accordingly.
     *
     * Based on the spaghetti code at
     * https://blog.kulturbanause.de/2011/08/wordpress-breadcrumb-navigation-ohne-plugin/
     *
     * @return void
     */
    public function detect()
    {
        if (!(is_home() || is_front_page()) || is_paged()) {
            $this->__detectPage();
            $this->__detectPagination();
        }

        // Mark the last breadcrumb as active
        if (!empty($this->__breadcrumbs)) {
            end($this->__breadcrumbs)->active = true;
            reset($this->__breadcrumbs); // set the array pointer to the first element again
        }
    }

    /**
     * Detects the breadcrumbs for the current page, but does not consider
     * pagination
     *
     * Based on the spaghetti code at
     * https://blog.kulturbanause.de/2011/08/wordpress-breadcrumb-navigation-ohne-plugin/
     *
     * @return void
     */
    private function __detectPage()
    {
        $homeLink = get_bloginfo('url');

        global $post;
        $this->createBreadcrumb($this->__homeName, $homeLink);

        if (is_search()) {
            $this->createBreadcrumb('Suchergebnisse für "' . get_search_query() . '"');
            return;
        }

        if (is_tag()) {
            $this->createBreadcrumb('Beiträge mit dem Schlagwort "' . single_tag_title('', false) . '"');
            return;
        }

        if (is_404()) {
            $this->createBreadcrumb('Fehler 404');
            return;
        }

        if (is_category()) {
            global $wp_query;
            $this->__addTermAndParents(get_term($wp_query->get_queried_object()->term_id));
            return;
        }

        if (is_day() || is_month() || is_year()) {
            // Year
            $year = get_the_time('Y');
            $this->createBreadcrumb($year, get_year_link($year));

            if (is_month() || is_day()) {
                // Month of year
                $month = get_the_time('m');
                $this->createBreadcrumb(get_the_time('F'), get_month_link($year, $month));

                if (is_day()) {
                    // Day
                    $day = get_the_time('d');
                    $this->createBreadcrumb($day, get_day_link($year, $month, $day));
                }
            }
            return;
        }

        if (is_attachment()) {
            $parent = get_post($post->post_parent);
            $category = get_the_category($parent->ID)[0];
            $this->__addTermAndParents($category);
            $this->createBreadcrumb($parent->post_title, get_permalink($parent));
            $this->createBreadcrumb(get_the_title());
            return;
        }

        // Custom taxonomy archive pages
        if (is_tax()) {
            $taxonomyName = get_query_var('taxonomy');
            $taxonomy = get_taxonomy($taxonomyName);
            $taxonomySlug = $taxonomy->rewrite['slug'];
            $this->createBreadcrumb($taxonomy->labels->name, "$homeLink/$taxonomySlug/");

            $term = get_term_by('slug', get_query_var('term'), $taxonomyName);
            $this->__addTermAndParents($term, $taxonomyName);

            return;
        }

        // Pages
        if (is_page()) {
            $this->__addPageAndParents($post);
            return;
        }

        // Single post pages
        if (is_single()) {
            if (get_post_type() != 'post') {
                // Posts with custom post types
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite['slug'];
                $this->createBreadcrumb($post_type->labels->name, "$homeLink/$slug/");
            } else {
                // Posts
                $deepestCategories = $this->__getDeepestTerms(get_the_category());
                $this->__addTermAndParents($deepestCategories[0]);
            }
            $this->createBreadcrumb(get_the_title());
        } else {
            // Archive pages with custom post types
            if (get_post_type() != 'post') {
                $post_type = get_post_type_object(get_post_type());
                $this->createBreadcrumb($post_type->labels->name);
                return;
            }
        }
    }

    /**
     * Adds a pagination breadcrumb if the current page is paginated.
     *
     * @return void
     */
    private function __detectPagination()
    {
        if (get_query_var('paged')) {
            $this->createBreadcrumb('Seite ' . get_query_var('paged'));
            return;
        }
    }

    /**
     * Adds breadcrumbs for a given term and each of its parents.
     *
     * @param WP_Term $term The term whose parents shall be iterated over
     * @return void
     */
    private function __addTermAndParents($term, $taxonomy = 'category')
    {
        $breadcrumbs = [];
        while ($term) {
            $breadcrumbs[] = new Breadcrumb($term->name, get_term_link($term, $taxonomy));
            if ($term->parent) {
                $term = get_term($term->parent, $taxonomy); // iterate to parent
            } else {
                $term = false;
            }
        }
        $this->addBreadcrumbs($breadcrumbs, $reverse = true);
    }

    /**
     * Adds breadcrumbs for a page (specified by $page) and each of its parent
     * pages.
     *
     * @param WP_Post $page The page whose parents shall be iterated over
     * @return void
     */
    private function __addPageAndParents($page)
    {
        $breadcrumbs = [];
        while ($page) {
            $breadcrumbs[] = new Breadcrumb(get_the_title($page->ID), get_permalink($page->ID));
            $parent_id = $page->post_parent;
            if ($parent_id) {
                $page = get_page($parent_id);
            } else {
                $page = false;
            }
        }
        $this->addBreadcrumbs($breadcrumbs, $reverse = true);
    }

    /**
     * Given an array of term objects, returns the terms for which the passed
     * term array contains no children.
     *
     * @param WP_Term[] $terms
     * @return WP_Term[]
     */
    private function __getDeepestTerms($terms)
    {
        $terms_by_id = [];
        foreach ($terms as $term) {
            $terms_by_id[$term->term_id] = $term;
        }
        // Unset all categories which at least one other category has a parent
        // pointer to
        foreach ($terms as $term) {
            unset($terms_by_id[$term->parent]);
        }
        return array_values($terms_by_id);
    }

    /**
     * Appends a breadcrumb to the end of the breadcrumb list.
     *
     * @param Breadcrumb $breadcrumb
     * @return void
     */
    public function addBreadcrumb($breadcrumb)
    {
        $this->__breadcrumbs[] = $breadcrumb;
    }

    /**
     * Appends an array of breadcrumbs to the end of the breadcrumb list.
     *
     * @param Breadcrumb[] $breadcrumbs
     * @param boolean $reverse Whether the passed array of breadcrumbs should be reversed (defaults to false)
     *
     * @return void
     */
    public function addBreadcrumbs($breadcrumbs, $reverse = false)
    {
        if ($reverse) {
            $breadcrumbs = array_reverse($breadcrumbs);
        }
        $this->__breadcrumbs = array_merge($this->__breadcrumbs, $breadcrumbs);
    }

    /**
     * Creates a new breadcrumb with the parameters passed and adds it to the
     * breadcrumb list.
     *
     * @param string $title The breadcrumb's title
     * @param string $url The url that the breadcrumb links to
     * @return Breadcrumb The newly created Breadcrumb object
     */
    public function createBreadcrumb($title, $url = '')
    {
        $breadcrumb = new Breadcrumb($title, $url);
        $this->addBreadcrumb($breadcrumb);
        return $breadcrumb;
    }

    /**
     * Uses the passed Formatter to generate a string from this instance's
     * breadcrumbs.
     *
     * @param Formatters\Formatter $formatter
     * @return string The resulting breadcrumbs string (plain text or HTML,
     *         depending on the Formatter)
     */
    public function format($formatter)
    {
        if (empty($this->__breadcrumbs)) {
            return '';
        }

        $output = $formatter->getPreList();

        $lastIndex = count($this->__breadcrumbs) - 1;
        foreach ($this->__breadcrumbs as $index => $breadcrumb) {
            $isLast = ($index == $lastIndex);
            $output .= $formatter->getPreBreadcrumb($breadcrumb, $isLast);
            $output .= $formatter->getBreadcrumb($breadcrumb);
            $output .= $formatter->getPostBreadcrumb($breadcrumb, $isLast);
        }

        return $output . $formatter->getPostList();
    }
}
