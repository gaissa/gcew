<?php

/**
 * TODO
 *
 * TODO
 *
 * @package GoogleCalendarEventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class GCEventWorkerPluginOptions
{
    /**
     * The constructor.
     *
     * TODO
     *
     */
    function __construct()
    {
        add_action('admin_menu', array($this, 'plugin_setup_menu'));
        add_action('admin_init', array($this, 'register_settings' ) );
    }

    /**
     * Setup the options page and link to the menu.
     *
     * TODO
     *
     */
    function plugin_setup_menu()
    {
        add_menu_page('gcew Options Page',
                      'gcew Options',
                      'manage_options',
                      'gcew-options',
                      array($this, 'init_settings_page' ));
    }

    /**
     * TODO
     *
     * TODO
     *
     */
    function init_settings_page()
    {
        ?>
        <div class="wrap">

            <h2><?php _e('Settings', 'event-worker-translations'); ?></h2>

            <form method="post" action="options.php">
            <?php
                settings_fields('settings-group');  // This prints out all hidden setting fields
                do_settings_sections('gcew-settings-sections');
                submit_button(__('Save Changes', 'event-worker-translations'));
            ?>
            </form>

        </div>
        <?php
    }

    /**
     * Register the setttings.
     *
     * TODO
     *
     */
    function register_settings()
    {
        add_settings_section('gcew-settings-section-first',
                             __('gcew Options', 'event-worker-translations'),
                             array($this, 'print_api_key_settings_section_info'),
                             'gcew-settings-sections');

        add_settings_field('api-key',
                           __('API KEY', 'event-worker-translations'),
                           array($this, 'create_input_api_key'),
                           'gcew-settings-sections',
                           'gcew-settings-section-first');

        add_settings_field('calendar-id',
                           __('CALENDAR ID', 'event-worker-translations'),
                           array($this, 'create_input_calendar_id'),
                           'gcew-settings-sections',
                           'gcew-settings-section-first');

        add_settings_field('future-events',
                           __('UPCOMING EVENTS ONLY', 'event-worker-translations'),
                           array($this, 'create_input_future_events'),
                           'gcew-settings-sections',
                           'gcew-settings-section-first');
    }

    /**
     * Print the settings info for the API endpoint.
     *
     */
    function print_api_key_settings_section_info()
    {
        _e('Set the options for fetching the events', 'event-worker-translations');
    }

    /**
     * Input for the API KEY.
     *
     */
    function create_input_api_key()
    {
        $options = get_option('gcew_api_key');
        ?>
        <input style="width:70%"
               id="api-key"
               type="text"
               name="gcew_api_key[api-key]"
               value="<?php echo esc_attr($options['api-key']); ?>" />
        <?php
    }

    /**
     * Input for the CALENDAR ID input.
     *
     * TODO
     *
     */
    function create_input_calendar_id()
    {
        $options = get_option('gcew_api_key');
        ?>

        <p>
            <input placeholder="Add new Google Calendar ID" id="add-id" style="width:50%" type="text" name="gcew_api_key[calendar-id]" value="" />
            <a href=javascript:void(0); id="add">ADD</a>
        </p>

        <div id="calendar-id-list">
            <ul class="list"></ul>
        </div>

        <script type="text/javascript">

        jQuery(document).ready(function()
        {
            var idCheck;
            var calendarIDList;

            var options =
            {
                item: '<li><text contenteditable="plaintext-only" class="name"></text><text class="city"></text></li>',
            };

            loadCalendarList();

            jQuery("#submit").click(function(e)
            {
                e.preventDefault();

                var api_key;
                var info = [];
                var future_event;

                var cusid_ele = document.getElementsByClassName('name');

                for (var j = 0; j < cusid_ele.length; j++)
                {
                    var item = cusid_ele[j];
                    checkPageExist(item.innerText, false);
                    info[j] = item.innerText;
                }

                api_key = jQuery("#api-key").val();
                future_events = jQuery("#future-events:checked").size();

                jQuery.post( "<?php echo plugins_url("plugin-options-helper.php", __FILE__); ?>", { user_id: info, api_key: api_key , future_events: future_events }, function(data)
                {
                   //console.log(data);
                }).done(function()
                {
                    if (idCheck === true)
                    {
                        alertify.success("You have saved your list.");
                    }
                });
            });

            function loadCalendarList()
            {
                var values = [];

                jQuery.post("<?php echo plugins_url("plugin-options-loader.php", __FILE__); ?>", function(data)
                {
                    values = (JSON.parse(data));

                    if (values.length != 0)
                    {
                        calendarIDList = new List('calendar-id-list', options, values);
                    }
                    else
                    {
                        calendarIDList = new List('calendar-id-list', options);
                    }
                })
                .done(function()
                {
                    jQuery(".removeID").live("click", function(e)
                    {
                        calendarIDList.remove("id", e.target.id);
                    });

                    jQuery(".name").live("focusin", function(e)
                    {
                        jQuery(".name").keypress(function(e){ return e.which != 13; });
                    });

                    jQuery(".name").live("focusout", function(e)
                    {
                        checkPageExist(jQuery(e.target).text(), true);
                    });

                    jQuery("#add").live("click", function(e)
                    {
                        checkPageExist(jQuery("#add-id").val(), false);

                        if (idCheck == true)
                        {
                            calendarIDList.add(
                            {
                                id: jQuery("#add-id").val(),
                                name: jQuery("#add-id").val(),
                                city: '<a href=javascript:void(0); id="' +
                                      jQuery("#add-id").val() +
                                      '" class="removeID">REMOVE</a>'
                            });
                        }
                    });
                });
            }

            function checkPageExist(id, bool)
                {
                    jQuery.ajax(
                    {
                        url: "https://www.googleapis.com/calendar/v3/calendars/" +
                             id +
                             "/events?maxResults=1&singleEvents=false&key=" +
                             jQuery("#api-key").val(),
                        async: bool,
                        statusCode:
                        {
                            404: function ()
                            {
                               idCheck = false;
                            }
                        },
                        success: function ()
                        {
                            jQuery(".alertify-button-ok").css("background-color", "#5CB811");
                            idCheck = true;
                        },
                        error: function ()
                        {
                            alertify.alert("Calendar ID is not valid!");
                            jQuery(".alertify-button-ok").css("background-color", "red");
                            idCheck = false;
                        }
                    });
                }
        });

        </script>

        <?php
    }

    /**
     * Input for the FUTURE EVENTS checkbox.
     *
     */
    function create_input_future_events()
    {
        $options = get_option('gcew_api_key');

        ?>
        <input type='checkbox'
               name='gcew_api_key[future-events]'
               id="future-events"
               value='1' <?php checked( $options['future-events'], 1); ?> />
        <?php
    }

} //end class

new GCEventWorkerPluginOptions();

/* End of File */