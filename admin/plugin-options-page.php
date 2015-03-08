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
                      'gcew Events Options',
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
                             __('gcew Events Options', 'event-worker-translations'),
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

        register_setting('settings-group',
                         'gcew_api_key',
                         array($this, 'plugin_api_endpoint_settings_validate'));
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
            <input id="add-id" style="width:50%" type="text" name="gcew_api_key[calendar-id]" value="" />
            <a href=javascript:void(0); id="add">ADD</a>
        </p>

        <div id="hacker-list">
            <ul class="list"></ul>
            <ul class="pagination"></ul>
        </div>

        <!-- <a href='#' id="saveAll">SAVE</a> -->

        <script type="text/javascript">

        jQuery(document).ready(function()
        {
            var hackerList;

            var options = {
                item: '<li><text contenteditable="plaintext-only" class="name"></text><text class="city"></text></li>',
            };

            var idCheck;

            loadToDo();

            //jQuery("#add").click(function(e)
            //{
                //var len = hackerList['items'].length;
                //hackerList.add({ id: jQuery("#add-id").val(), name: jQuery("#add-id").val(), city: '<a href=javascript:void(0); id="' + jQuery("#add-id").val() + '" class="removeID">REMOVE</a>' });
            //});

            //jQuery("#saveAll").click(function(e)
            jQuery("#submit").click(function(e)
            {
                //e.preventDefault();

                var info = [];

                //for (var i = 0; i < hackerList['items'].length; i++)
                //{
                    var cusid_ele = document.getElementsByClassName('name');

                    for (var j = 0; j < cusid_ele.length; j++)
                    {
                        var item = cusid_ele[j];
                        info[j] = item.innerText;
                    }
                //}

                //console.log(info);
                //info[0] = '...@group.calendar.google.com'
                //info[1] = '...@group.calendar.google.com'

                jQuery.post( "<?php echo plugins_url("plugin-options-helper.php", __FILE__); ?>", { user_id: info  }, function(data)
                {
                    //console.log(data);
                }).done(function()
                {
                    alertify.success("You have saved your list.");
                });
            });

            function loadToDo()
            {
                var values = [];

                jQuery.post("<?php echo plugins_url("plugin-options-loader.php", __FILE__); ?>", function(data)
                {
                    values = (JSON.parse(data));

                    if (values.length != 0)
                    {
                        hackerList = new List('hacker-list', options, values);
                    }
                    else
                    {
                        hackerList = new List('hacker-list', options);
                    }
                })
                .done(function()
                {
                    jQuery(".removeID").live("click", function(e)
                    {
                        hackerList.remove("id", e.target.id);
                    });

                    jQuery(".name").live("focusin", function(e)
                    {
                        jQuery(".name").keypress(function(e){ return e.which != 13; });
                    });

                    jQuery(".name").live("focusout", function(e)
                    {
                        checkPageExist(jQuery(e.target).text());
                    });

                    jQuery("#add").live("click", function(e)
                    {
                        checkPageExist(jQuery("#add-id").val());

                        if (idCheck == true)
                        {
                            hackerList.add({   id: jQuery("#add-id").val(),
                                             name: jQuery("#add-id").val(),
                                             city: '<a href=javascript:void(0); id="' +
                                                   jQuery("#add-id").val() +
                                                   '" class="removeID">REMOVE</a>' });
                        }
                    });
                });

                function checkPageExist(id)
                {
                    jQuery.ajax(
                    {
                        url: "https://www.googleapis.com/calendar/v3/calendars/" +
                             id +
                             "/events?singleEvents=true&key=AIzaSyAjYsypMI0vpY6hwkrdiHn7GcjPbPXFnDQ",
                        async: false,
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
               value='1' <?php checked( $options['future-events'], 1); ?> />
        <?php
    }

    /**
     * Validate the input.
     *
     * @param  array $arr_input the input.
     *
     * @return array
     *
     */
    function plugin_api_endpoint_settings_validate($arr_input)
    {
        $options = get_option('gcew_api_key');
        $options['api-key'] = sanitize_text_field($arr_input['api-key']);
        //$options['calendar-id'] = sanitize_text_field($arr_input['calendar-id']);

        $options['future-events'] = $arr_input['future-events'];

        return $options;
    }

} //end class

new GCEventWorkerPluginOptions();

/* End of File */