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
class GCEventWorkerPluginOptionsLoader
{
    /**
     * The constructor.
     *
     */
    function __construct()
    {
        require_once('../../../../wp-blog-header.php');

        $options = get_option('gcew_api_key');
        $list = $options['calendar-id'][0];

        $t = array();

        for ($i = 0; $i < count($list); $i++)
        {
            $associativeArray = array();
            $associativeArray ['id'] = $list[$i];
            $associativeArray ['name'] =  $list[$i];
            $associativeArray ['city'] = '<a href=javascript:void(0); id="' . $list[$i] . '" class="removeID">REMOVE</a>';
            $associativeArray ['list'] = $list;

            $t[] = $associativeArray;
        }

        echo(json_encode($t));
    }

} //end class

new GCEventWorkerPluginOptionsLoader();

/* End of File */