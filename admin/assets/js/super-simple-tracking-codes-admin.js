( function() {
    // Add current hash to the form action. 
	function setTabHash() {
		var conf = jQuery( "#sstc-form" );
		if ( conf.length ) {
			var currentUrl = conf.attr( "action" ).split( "#" )[ 0 ];
			conf.attr( "action", currentUrl + window.location.hash );
		}
    }
    
    // On hash change reset the form action.
	jQuery( window ).on( "hashchange", setTabHash );

    // Set the initial active tab in the settings page
    function setInitialActiveTab() {
		var activeTabId = window.location.hash.replace( "#top#", "" );

        if ( activeTabId.search( "#top" ) !== -1 ) {
			activeTabId = window.location.hash.replace( "#top%23", "" );
		}

        if ( "" === activeTabId || "#" === activeTabId.charAt( 0 ) ) {
            activeTabId = jQuery( ".tab-panel" ).attr( "id" );
		}

		jQuery( "#" + activeTabId ).addClass( "active" );
		jQuery( "#" + activeTabId + "-tab" ).addClass( "nav-tab-active" ).click();
	}

    jQuery( document ).ready( function() {
        // On hash change reset the form action.
        setTabHash();

        // Enable WP color picker.
        jQuery( function() {
            jQuery( ".sstc-color-field" ).wpColorPicker();
        } );

        // Handle the settings pages tabs.
        jQuery( "#sstc-tabs" ).find( "a" ).click( function() {
            jQuery( "#sstc-tabs" ).find( "a" ).removeClass( "nav-tab-active" );
            jQuery( ".tab-panel" ).hide();

            var id = jQuery( this ).attr( "id" ).replace( "-tab", "" );
            var activeTab = jQuery( "#" + id );

            activeTab.show();
            jQuery( this ).addClass( "nav-tab-active" );

            if ( activeTab.hasClass( "nosave" ) ) {
                jQuery( "#submit" ).hide();
            } else {
                jQuery( "#submit" ).show();
            }
        } );

        // Show/Hide cookie consent settings.
        jQuery( function() {
            jQuery( "#gdpr_enable" ).on( "change", function() {
                if( jQuery( this ).prop( "checked" ) ) {
                    jQuery( "#cookie-consent" ).show();
                } else {
                    jQuery( "#cookie-consent" ).hide();
                }
            } );
            jQuery( "#gdpr_enable" ).trigger( "change" );
        } );

        // Show/Hide 'Select specific pages' option.
        jQuery( function() {
            jQuery( "input[id$=_sitewide]" ).on( "change", function() {
                if( jQuery( this ).prop( "checked" ) ) {
                    jQuery( this ).closest( "tr" ).next().hide()
                } else {
                    jQuery( this ).closest( "tr" ).next().show();
                }
            });
            jQuery( "input[id$=_sitewide]" ).trigger( "change" );
        } );

        setInitialActiveTab();
    } );
}() );
