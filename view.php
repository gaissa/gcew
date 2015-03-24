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
    function __construct($output, $id_list)
    {
        $this->id_list = $id_list;
        add_shortcode('search', array(&$this, 'show_search_form'));
        add_shortcode('print', array(&$this, 'show_print_form'));

        add_shortcode('recent', array(&$this, 'show_recent_form'));

        add_shortcode('calendar', array(&$this, 'show_calendar'));

        add_shortcode('events_map', array(&$this, 'show_events_map'));
        add_shortcode('events_list', array(&$this, 'show_events_list'));

        add_filter('widget_text', 'do_shortcode');

        for ($i = 0; $i < count($output); $i++)
        {

            if ($output[$i]['start_time'] != "")
            {
                $start = '<strong>ALKAA:</strong> ' .
                $output[$i]['start_at'] .
                ' @ ' .
                $output[$i]['start_time'];

                $end = '<strong>LOPPUU:</strong> ' . $output[$i]['end_time'];
            }
            else
            {
                $start = '<strong>ALKAA:</strong> ' . $output[$i]['start_at'];

                $end = '';
            }

            $temp = '<a href="' . $output[$i]['link'] . '">Jatka kalenteriin &rarr;</a>';

            $args = array('slug' => $output[$i]['id'],
                          'post_title' => $output[$i]['name'],
                          'post_content' => $start .
                                            '<br>' .
                                            $end .
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
        $searchform = '<input class="search" placeholder="Etsi Tapahtumia"/>';

        return $searchform;
    }

    /**
     * TODO
     *
     * @return string
     *
     */
    function show_print_form($atts)
    {
       $printform = "";

       $atts = shortcode_atts(
        array(
            'pdf' => 'true',
            'text' => 'true',
        ), $atts, 'print' );

        if ($atts['pdf'] === "true")
        {
            $printform .= '<div id="linker">PDF</div>';
        }
        if ($atts['text'] === "true")
        {
            $printform .= '<div id="linker">TEKSTITIEDOSTO</div>';
        }

        return $printform;
    }

    /**
     * TODO
     *
     * @return string
     *
     */
    function show_recent_form($atts)
    {
        extract(shortcode_atts(array(
            'width' => 400,
            'height' => 200,
        ), $atts));

        return '<img src="http://lorempixel.com/'. $width . '/'. $height . '" />';
    }

    /**
     * TODO
     *
     * @return string
     *
     */
    function show_calendar($atts)
    {
        extract(shortcode_atts(array(
            'height' => '800px'
        ), $atts));

        $calendars = "";

        for ($i = 0; $i < count($this->id_list); $i++)
        {
            $calendars .= '&amp;src=' . $this->id_list[$i] . '&amp;color=%2328754E';
        }

        $temp = '<iframe src="https://www.google.com/calendar/embed' .
                '?showTitle=0' .
                '&amp;wkst=1' .
                '&amp;bgcolor=%23FFFFFF' .
                $calendars .
                '&amp;ctz=Europe%2FHelsinki"' .
                'style=border-width:0 width="100%" height="' . $height . 'px" frameborder="0" scrolling="no">' .
                '</iframe>';

        return $temp;
    }

    /**
     * TODO
     *
     * @param  array $atts
     *
     * @return string
     *
     */
    function show_events_map($atts)
    {
        $loader = '<div></div>';

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
    function show_events_list($atts)
    {
        $loader = '<div id="event-list">
                   <ul class="paginationTop"></ul>

                   <h4 id="event_search_results">HAKUTULOKSET:</h4>
                   <ul class="list"></ul>

                   <br>

                   <ul class="paginationBottom"></ul>

                   </div>';

        return $loader;
    }

} //end class


/* End of File */