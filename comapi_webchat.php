<?php
   /*
    Plugin Name: Comapi Webchat Plugin
    Plugin URI: https://docs.comapi.com/
    description: Installs the Comapi Webchat Widget into your Wordpress site
    Version: 1.0
    Author: Dave Baddeley
    Author URI: https://www.comapi.com/
    License: GPLv3 or later
   
    Copyright Comapi (email : info@comapi.com )
    
    Comapi Webchat Plugin is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    any later version.
    
    Comapi Webchat Plugin is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with Comapi Webchat Plugin. If not, see <http://www.gnu.org/licenses/>.
    */

    /* Plugin code */
    /***************/

    function cww_add_widget_code() {
        $options = get_option(OPTION_FIELDS, array());
        echo '<script> window.comapiConfig = { apiSpace: \''.$options['apispace'].'\' }; (function(d, s, id){ var js, cjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) {return;} js = d.createElement(s); js.id = id; js.src = \'//cdn.dnky.co/widget/bootstrap.js\'; cjs.parentNode.insertBefore(js, cjs); }(document, \'script\', \'comapi-widget\')); </script>';
        return;
    }

    // Register the hook to render the widget
    add_action( 'wp_footer', 'cww_add_widget_code', 11 );

    /* Admin page */
    /**************/
    const OPTION_GROUP = 'cww_options';
    const OPTION_FIELDS = 'cww_options_fields';
    const GENERAL_SETTINGS = 'cww_plugin_main';
    
    /**
    * register our cww_settings_init to the admin_init action hook
    */
    if ( is_admin() ){ // admin actions
        // Register settings page        
        add_action('admin_menu', 'cww_options_page');
        add_action('admin_init', 'cww_settings_init');
      }
    
    /**
     * custom option and settings
     */
    function cww_options_page() {
        add_options_page('Comapi Webchat Plugin Settings', 'Comapi Webchat Settings', 'manage_options', OPTION_GROUP, 'cww_options_page_html');
    }

    function cww_settings_init() {
        register_setting( OPTION_FIELDS, OPTION_FIELDS, 'cww_plugin_options_validate' );
        add_settings_section(GENERAL_SETTINGS, 'Main Settings', 'cww_plugin_section_text', OPTION_GROUP);
        add_settings_field('cww_plugin_apispace', 'API Space', 'cww_field_apispace_cb', OPTION_GROUP, GENERAL_SETTINGS);        
    }

    /**
     * custom option and settings:
     * callback functions
     */

    // Renders the settings section title
    function cww_plugin_section_text() {
        echo '<p>The settings to configure the webchat widgets behavior.</p>';
    }

    // Api space field cb
    function cww_field_apispace_cb( $args ) {
        $options = get_option(OPTION_FIELDS);
        echo "<input id='cww_plugin_apispace' name='cww_options_fields[apispace]' size='37' type='text' value='{$options['apispace']}' />";
    }
    
    // Field validation
    function cww_plugin_options_validate($input) {
        $options = get_option(OPTION_FIELDS);
        $options['apispace'] = trim($input['apispace']);
        if(!preg_match('/^[a-z0-9\-]{36}$/i', $options['apispace'])) {
            // Invalid
            $options['apispace'] = '';
            add_settings_error(
                "cww_err_invalid_apispace",
                "cww_invalid_apispace",
                "This is not a valid GUID; your API Space Id will look like: c99acf6e-4352-4b26-a71a-c3032bea7b12",
                "error");
        }
        return $options;
    }

    // Admin page markup
    function cww_options_page_html()
    {
        ?>
        <div class="wrap">
            <?php
                echo '<a href="https://comapi.com" target="_new"><img src="' . plugins_url( 'images/comapi_easy.jpg', __FILE__ ) . '" style="height: 300px;"></a>';
            ?>
            <p>This plugin quickly and easily installs the Comapi webchat widget across your Wordpress site 
            allowing you to chat in real time to your visitors, as well as allowing you to answer customers questions from other channels such as:</p>
            <ul style="list-style-type: circle;">
                <li>Facebook Messenger</li>
                <li>SMS</li>
                <li>Email</li>
                <li>Twitter DM</li>
                <li>Plus more...</li>
            </ul>            
            
            <p>Find out more or sign up <b>for a free no obligation</b> trial at <a href="https://comapi.com" target="_new">comapi.com</a>
            </p>
            <hr/>
            <form action="options.php" method="post">
                <?php
                // output security fields for the registered settings
                settings_fields(OPTION_FIELDS);
                // output setting sections and their fields
                do_settings_sections(OPTION_GROUP);
                // output save settings button
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }
?>