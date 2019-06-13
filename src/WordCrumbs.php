<?php
/**
 * The main WordCrumbs class
 *
 * @author bjoluc <25661029+bjoluc@users.noreply.github.com>
 * @version 1.0.0
 *
 * @license GPL-3.0-or-later
 */

namespace bjoluc\WordCrumbs;

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

class WordCrumbs
{
    protected $_homeName;

    protected $_homeLink;

    protected $_breadcrumbs;

    protected $_translator;

    protected $_allCustomTaxonomiesEnabled = false;

    protected $_enabledCustomTaxonomies = [];

    protected $_allCustomPostTypesEnabled = false;

    protected $_enabledCustomPostTypes = [];

    protected $_termListPagesEnabledForAllTaxonomies = false;

    protected $_taxonomiesWithTermListPages = [];

    protected $_categoriesEnabled = true;

    protected $_postsEnabled = true;

    protected $_pagesEnabled = true;

    protected $_searchEnabled = true;

    protected $_error404Enabled = true;

    protected $_tagsEnabled = true;

    protected $_datesEnabled = true;

    protected $_attachmentsEnabled = true;

    /**
     * Creates a new WordCrumbs object.
     *
     * @param string $locale (optional) The locale to be used. Defaults to the
     *        result of get_locale(). If the corresponding locale file is not
     *        found, en_US is used as a fallback.
     */
    public function __construct($locale = null)
    {
        if ($locale === null) {
            $locale = get_locale();
        }

        $localeFile = __DIR__ . "/../locales/$locale.yaml";

        $this->_translator = new Translator($locale);
        $this->_translator->addLoader('yaml', new YamlFileLoader());
        if (file_exists($localeFile)) {
            $this->_translator->addResource('yaml', $localeFile, $locale);
        }
        $this->_translator->setFallbackLocales(['en_US']);
        $this->_translator->addResource('yaml', __DIR__ . "/../locales/en_US.yaml", 'en_US');

        $this->_homeName = $this->_translator->trans('wordcrumbs.home');
        $this->_breadcrumbs = [];
    }

    /**
     * Set the name of the "Home" breadcrumb (defaults to 'Home' or its
     * respective translation).
     *
     * @param string $homeName The name of the "Home" breadcrumb
     * @return void
     */
    public function setHomeName($homeName)
    {
        $this->_homeName = $homeName;
    }

    /**
     * Disables the generation of breadcrumbs for posts. Custom post types are
     * not affected.
     *
     * @return void
     */
    public function disablePosts()
    {
        $this->_postsEnabled = false;
    }

    /**
     * Disables the generation of breadcrumbs for pages.
     *
     * @return void
     */
    public function disablePages()
    {
        $this->_pagesEnabled = false;
    }

    /**
     * Disables the generation of breadcrumbs for Categories. Custom taxonomies
     * are not affected.
     *
     * @return void
     */
    public function disableCategories()
    {
        $this->_categoriesEnabled = false;
    }

    /**
     * Disables the generation of breadcrumbs for search results pages.
     *
     * @return void
     */
    public function disableSearch()
    {
        $this->_searchEnabled = false;
    }

    /**
     * Disables the generation of breadcrumbs on the 404 error page.
     *
     * @return void
     */
    public function disableError404()
    {
        $this->_error404Enabled = false;
    }

    /**
     * Disables the generation of breadcrumbs for tag archives.
     *
     * @return void
     */
    public function disableTags()
    {
        $this->_tagsEnabled = false;
    }

    /**
     * Disables the generation of breadcrumbs for date archives.
     *
     * @return void
     */
    public function disableDates()
    {
        $this->_datesEnabled = false;
    }

    /**
     * Disables the generation of breadcrumbs for attachments.
     *
     * @return void
     */
    public function disableAttachments()
    {
        $this->_attachmentsEnabled = false;
    }

    /**
     * By default, breadcrumbs for custom taxonomy terms are disabled. Call this
     * method to enable them for the terms of either all or specific custom
     * taxonomies.
     *
     * @param string[] $taxonomyNames (optional) The names of the custom
     *        taxonomies for which to enable breadcrumbs. If left out,
     *        breadcrumbs will be enabled for all custom taxonomies.
     * @return void
     */
    public function enableCustomTaxonomies(array $taxonomyNames = null)
    {
        if ($taxonomyNames === null) {
            $this->_allCustomTaxonomiesEnabled = true;
        } else {
            $this->_enabledCustomTaxonomies = $taxonomyNames;
        }
    }

    /**
     * By default, breadcrumbs for custom post types are disabled (no
     * breadcrumbs will be checkPagination for posts with a custom post type). Call
     * this method to enable breadcrumbs for either all or specific custom post
     * types.
     *
     * @param string[] $postTypes (optional) The names of the custom post types
     *        for which to enable breadcrumbs. If left out, breadcrumbs will be
     *        enabled for all custom post types.
     * @return void
     */
    public function enableCustomPostTypes(array $postTypes = null)
    {
        if ($postTypes === null) {
            $this->_allCustomPostTypesEnabled = true;
        } else {
            $this->_enabledCustomPostTypes = $postTypes;
        }
    }

    /**
     * Enables linked taxonomy breadcrumbs for all or specific taxonomies. For
     * example if you have a page at /colors where terms of the taxonomy 'color'
     * are listed and you call enableTermListPages() or
     * enableTermListPages(['color']), color terms will have the breadcrumb
     * 'Colors' linking to /color.
     *
     * @param string[] $taxonomyNames (optional) The names of taxonomies for
     *        which you have term list pages. If omitted, applies to all
     *        taxonomies.
     * @return void
     */
    public function enableTermListPages($taxonomyNames = null)
    {
        if ($taxonomyNames == null) {
            $this->_termListPagesEnabledForAllTaxonomies = true;
        } else {
            $this->_taxonomiesWithTermListPages = $taxonomyNames;
        }
    }

    /**
     * Returns whether breadcrumbs are enabled for the provided taxonomy.
     *
     * @param string $taxonomyName The name of the taxonomy to check for
     * @return boolean
     */
    protected function _isEnabledTaxonomy($taxonomyName)
    {
        if ($taxonomyName === 'category') {
            return $this->_categoriesEnabled;
        }
        return $this->_allCustomTaxonomiesEnabled || in_array($taxonomyName, $this->_enabledCustomTaxonomies);
    }

    /**
     * Returns whether breadcrumbs are enabled for the provided post type.
     *
     * @param string $postType The name of the post type to check for
     * @return boolean
     */
    protected function _isEnabledPostType($postType)
    {
        if ($postType === 'post') {
            return $this->_postsEnabled;
        }
        return $this->_allCustomPostTypesEnabled || in_array($postType, $this->_enabledCustomPostTypes);
    }

    /**
     * Returns whether the provided custom taxonomy has a term list page.
     *
     * @param string $taxonomyName The name of the custom taxonomy to check for
     * @return boolean
     */
    protected function _isTaxonomyWithTermListPage($taxonomyName)
    {
        return $this->_termListPagesEnabledForAllTaxonomies || in_array($taxonomyName, $this->_taxonomiesWithTermListPages);
    }

    /**
     * Uses WordPress functions to detect the page hierarchy and add breadcrumbs
     * accordingly.
     *
     * A clean, enhanced rewrite of the code at
     * https://blog.kulturbanause.de/2011/08/wordpress-breadcrumb-navigation-ohne-plugin/
     *
     * @return void
     */
    public function detect()
    {

        if (!(is_home() || is_front_page()) || is_paged()) {
            $checkPagination = false;

            if (is_home()) {
                $this->_addHome();
                $checkPagination = true;
            } elseif (is_category()) {
                if ($this->_categoriesEnabled) {
                    $this->_processCategoryArchive();
                    $checkPagination = true;
                }
            } elseif (is_search()) {
                if ($this->_searchEnabled) {
                    $this->_processSearch();
                    $checkPagination = true;
                }
            } elseif (is_tag()) {
                if ($this->_tagsEnabled) {
                    $this->_processTag();
                    $checkPagination = true;
                }
            } elseif (is_404()) {
                if ($this->_error404Enabled) {
                    $this->_processError404();
                }
            } elseif (is_day() || is_month() || is_year()) {
                if ($this->_datesEnabled) {
                    $this->_processDate();
                    $checkPagination = true;
                }
            } elseif (is_attachment()) {
                if ($this->_attachmentsEnabled) {
                    $this->_processAttachment();
                }
            } elseif (is_tax()) {
                $taxonomyName = get_query_var('taxonomy');
                if ($this->_isEnabledTaxonomy($taxonomyName)) {
                    $this->_processCustomTaxonomyArchive($taxonomyName);
                    $checkPagination = true;
                }

            } elseif (is_page()) {
                if ($this->_pagesEnabled) {
                    $this->_processPage();
                    $checkPagination = true;
                }
            } else {
                $postType = get_post_type();
                if (is_single()) {
                    // Single post
                    if ($postType == 'post') {
                        // Default post type
                        if ($this->_postsEnabled) {
                            $this->_processSinglePost();
                            $checkPagination = true;
                        }
                    } else {
                        // Custom post type
                        if ($this->_isEnabledPostType($postType)) {
                            $this->_processSingleCustomTypePost($postType);
                        }
                    }
                } else {
                    // Custom post type archive (Non-custom archives have been
                    // caught before)
                    if ($this->_isEnabledPostType($postType)) {
                        $checkPagination = $this->_processCustomPostTypeArchive();
                    }
                }
            }

            if ($checkPagination) {
                $this->_processPagination();
            }
        }

        // Mark the last breadcrumb as active
        if (!empty($this->_breadcrumbs)) {
            end($this->_breadcrumbs)->active = true;
            reset($this->_breadcrumbs); // set the array pointer to the first element again
        }
    }

    /**
     * Adds a home breadcrumb.
     *
     * @return void
     */
    protected function _addHome()
    {
        $this->createBreadcrumb($this->_homeName, get_home_url());
    }

    /**
     * Called by `detect()` on category archive pages.
     *
     * @return void
     */
    protected function _processCategoryArchive()
    {
        $this->_addHome();
        global $wp_query;
        $this->_addTermAndParents(get_term($wp_query->get_queried_object()->term_id));
    }

    /**
     * Called by `detect()` on search result pages.
     *
     * @return void
     */
    protected function _processSearch()
    {
        $this->_addHome();
        $this->createBreadcrumb($this->_translator->trans('wordcrumbs.search_results', ['{keywords}' => get_search_query()]));
    }

    /**
     * Called by `detect()` on tag archive pages.
     *
     * @return void
     */
    protected function _processTag()
    {
        $this->_addHome();
        $this->createBreadcrumb($this->_translator->trans('wordcrumbs.tag', ['{tag}' => single_tag_title('', false)]));
    }

    /**
     * Called by `detect()` on 404 pages.
     *
     * @return void
     */
    protected function _processError404()
    {
        $this->_addHome();
        $this->createBreadcrumb($this->_translator->trans('wordcrumbs.error_404'));
    }

    /**
     * Called by `detect()` on date archive pages.
     *
     * @return void
     */
    protected function _processDate()
    {
        $this->_addHome();
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
    }

    /**
     * Called by `detect()` on attachment pages.
     *
     * @return void
     */
    protected function _processAttachment()
    {
        $this->_addHome();
        $parent = get_post($post->post_parent);
        $category = get_the_category($parent->ID)[0];
        $this->_addTermAndParents($category);
        $this->createBreadcrumb($parent->post_title, get_permalink($parent));
        $this->createBreadcrumb(get_the_title());
    }

    /**
     * Called by `detect()` on custom taxonomy archive pages.
     *
     * @return void
     */
    protected function _processCustomTaxonomyArchive($taxonomyName)
    {
        $this->_addHome();
        $taxonomy = get_taxonomy($taxonomyName);

        $termListBreadcrumb = new Breadcrumb($taxonomy->labels->name);
        if ($this->_isTaxonomyWithTermListPage($taxonomyName)) {
            $termListBreadcrumb->url = get_site_url() . '/' . $taxonomy->rewrite['slug'] . '/';
        }
        $this->addBreadcrumb($termListBreadcrumb);

        $term = get_term_by('slug', get_query_var('term'), $taxonomyName);
        $this->_addTermAndParents($term, $taxonomyName);
    }

    /**
     * Called by `detect()` on WordPress pages.
     *
     * @return void
     */
    protected function _processPage()
    {
        $this->_addHome();
        global $post;
        $this->_addPageAndParents($post);
    }

    /**
     * Called by `detect()` on custom post archive pages.
     *
     * @return void
     */
    protected function _processCustomPostTypeArchive()
    {
        $this->_addHome();
        $post_type = get_post_type_object(get_post_type());
        $this->createBreadcrumb($post_type->labels->name);
    }

    /**
     * Called by `detect()` on single post pages.
     *
     * @return void
     */
    protected function _processSinglePost()
    {
        $this->_addHome();
        $deepestCategories = $this->_getDeepestTerms(get_the_category());
        $this->_addTermAndParents($deepestCategories[0]);
        $this->createBreadcrumb(get_the_title());
    }

    /**
     * Called by `detect()` on single post pages with a custom post type.
     *
     * @return void
     */
    protected function _processSingleCustomTypePost($postType)
    {
        $this->_addHome();

        $postType = get_post_type_object($postType);
        $archiveBreadcrumb = new Breadcrumb($postType->labels->name);
        if ($postType->has_archive) {
            $archiveBreadcrumb->url = get_site_url() . '/' . $postType->rewrite['slug'] . '/';
        }
        $this->addBreadcrumb($archiveBreadcrumb);

        $this->createBreadcrumb(get_the_title());
    }

    /**
     * Adds a pagination breadcrumb if the current page is paginated.
     *
     * @return void
     */
    protected function _processPagination()
    {
        $page = get_query_var('paged');
        if ($page) {
            $this->createBreadcrumb($this->_translator->trans('wordcrumbs.pagination', ['page' => $page]));
        }
    }

    /**
     * Adds breadcrumbs for a given term and each of its parents.
     *
     * @param WP_Term $term The term whose parents shall be iterated over
     * @return void
     */
    protected function _addTermAndParents($term, $taxonomy = 'category')
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
    protected function _addPageAndParents($page)
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
    protected function _getDeepestTerms($terms)
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
        $this->_breadcrumbs[] = $breadcrumb;
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
        $this->_breadcrumbs = array_merge($this->_breadcrumbs, $breadcrumbs);
    }

    /**
     * Creates a new breadcrumb with the parameters passed and adds it to the
     * breadcrumbs list.
     *
     * @param string $title The breadcrumb's title
     * @param string $url The url that the breadcrumb links to
     * @return Breadcrumb The new Breadcrumb object
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
     * @param Formatters\FormatterInterface $formatter
     * @return string The resulting breadcrumbs string (plain text or HTML,
     *         depending on the Formatter)
     */
    public function format($formatter)
    {
        if (empty($this->_breadcrumbs)) {
            return '';
        }

        $formatter->setTranslator($this->_translator);
        $output = $formatter->getPre();

        $lastIndex = count($this->_breadcrumbs) - 1;
        foreach ($this->_breadcrumbs as $index => $breadcrumb) {
            $isLast = ($index == $lastIndex);
            $output .= $formatter->getPreBreadcrumb($breadcrumb, $isLast);
            $output .= $formatter->getBreadcrumb($breadcrumb);
            $output .= $formatter->getPostBreadcrumb($breadcrumb, $isLast);
        }

        return $output . $formatter->getPost();
    }
}
