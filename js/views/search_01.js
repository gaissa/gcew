(function ()
{
    'use strict';
	
	jQuery(document).ready(function ()
	{
		get_search_form();	
    });	
	
	function get_search_form()
	{
		jQuery('#filter').keyup(function()
        {	
            // Retrieve the input field text and reset the count to zero
            var filter = jQuery(this).val(), count = 0;

            // Loop through the comment list
            jQuery('.list li').each(function()
            {
                // If the list item does not contain the text phrase fade it out
                if (jQuery(this).text().search(new RegExp(filter, 'i')) < 0)
                {
                    jQuery(this).hide();
					jQuery('.test').hide();					
					jQuery('.main').hide();
					jQuery('.pagination').hide();
					
					jQuery('#event_search_results').show();					
                }               
				else
				{
					jQuery(this).show();
					jQuery(this).css('margin-bottom', '31px');
				}			
            });
			
			if (jQuery('#filter').val() === "")
			{	
				jQuery('.test').show();
				jQuery('.main').show();
				jQuery('.pagination').show();
				
				jQuery('#event_search_results').hide();				
			}
        });
	}

})();