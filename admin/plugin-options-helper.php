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
class GCEventWorkerPluginOptionsHelper
{
    /**
     * The constructor.
     *
     */
    function __construct()
    {
        require_once('../../../../wp-blog-header.php');

        // Get entire array.
        $options = get_option('gcew_api_key');

         // Alter the options array appropriately.
        $options['api-key'] = $_POST['api_key'];
        $options['calendar-id'] = array($_POST['user_id']);
        $options['future-events'] = $_POST['future_events'];

        // Update entire array.
        update_option('gcew_api_key', $options);

        print_r($_POST['user_id']);
    }

} //end class

new GCEventWorkerPluginOptionsHelper();

/* End of File */