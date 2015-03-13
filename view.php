<?php

/**
 * TODO.
 *
 * TODO.
 *
 * @package GoogleCalendarEventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class GCEventWorkerView
{
    /**
     * The constructor.
     *
     */
    function __construct()
    {
        add_shortcode('search', array(&$this, 'show_search_form'));

        add_shortcode('events_01', array(&$this, 'show_event_list_01'));
        add_shortcode('events_02', array(&$this, 'show_event_list_02'));

        add_shortcode('categories_box', array(&$this, 'show_event_cat_box'));
        add_shortcode('categories_list', array(&$this, 'show_event_cat_list'));

        add_filter('widget_text', 'do_shortcode');

        $output = get_option('gcew_events_list');

        for ($i = 0; $i < count($output); $i++)
        {
            $temp = '<a href="' . $output[$i]['link'] . '">Linkki kalenteriin &rarr;</a>';

            $args = array('slug' => $output[$i]['id'],
                          'post_title' => $output[$i]['name'],
                          'post_content' => '<strong>ALKAA:</strong> ' . $output[$i]['start_at'] .
                                            ' @ ' .
                                            $output[$i]['start_time'] .
                                            '<br>' .
                                            '<strong>LOPPUU:</strong> ' . $output[$i]['end_time']  .
                                            '<br>' .
                                            $output[$i]['location'] . '<br><br>' .
                                            $output[$i]['description'] . '<br>' .
                                            $temp,
                          'post_date' => $output[$i]['single_post_time']
            );

            new GCEventWorkerVirtualPage($args);
        }
    }

    /**
     * TODO
     *
     * @return string
     *
     */
    function show_search_form()
    {
        $searchform = '<form id="live-search" action="" class="styled" method="post">
                       <fieldset>
                       <input type="text" placeholder="Etsi Tapahtumia" class="text-input" id="filter" value="" />
                       <span id="filter-count"></span>
                       </fieldset>
                       </form>';

        return $searchform;
    }

    /**
     * TODO
     *
     * @param  array $atts
     *
     * @return string
     *
     */
    function show_event_list_01($atts)
    {
        $loader = '<div>
                   <ul class="list">
                   <div id="loader"></div>
                   </ul>
                   </div>';

        return $loader;
    }

    /**
     * TODO
     *
     * @param  array $atts
     *
     * @return string
     *
     */
    function show_event_list_02($atts)
    {
        $loader = ' <div id="event-list">

                    <ul class="pagination"></ul>
                     <h4 id="event_search_results">HAKUTULOKSET:</h4>
                    <ul class="list"></ul>
                    </div>';

        return $loader;
    }

    /**
     * TODO
     *
     * @return string
     *
     */
    function show_event_cat_list()
    {
        $output = get_option('gcew_event_categories');
        $listview = '<div id="event_categories">';

        $listview .= '<a href="" class="confirmation">' . 'KAIKKI' . '</a><br>';

        for ($i = 0; $i < count($output); $i++)
        {
            $listview .= '<a href="" class="confirmation">' . $output[$i] . '</a><br>';
        }

        $listview .= '</div>';

        return $listview;
    }

    /**
     * TODO
     *
     * @return string
     *
     */
    function show_event_cat_box()
    {
        $output = get_option('gcew_event_categories');

        $selectbox = '<select id="selectBox" name="selectBox" style="width: 100%">';

        $selectbox .= '<option value="' . 'ALL' . '"></option>';

        $selectbox .= '</select>';

        return $selectbox;
    }

} //end class

new GCEventWorkerView();

/* End of File */