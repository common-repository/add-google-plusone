<?php
/*
    Plugin Name: Add Google PlusOne
    Description: Add the official Google +1 button to your WordPress blog to let visitors recommend your pages right in Google search results without having to leave the page.
    Author: Onnay Okheng
    Author URI: http://onnayokheng.com
    Version: 0.4

    Copyright (C) 2010-2010, Onnay Okheng
    All rights reserved.

    Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    Neither the name of Alex Moss or pleer nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

// define the URL
define ('ON_GOOGLE1_DIR', WP_PLUGIN_DIR.'/add-google-plusone/');
define ('ON_GOOGLE1_URL', WP_PLUGIN_URL.'/add-google-plusone/');

// Checking page if is admin page
if(is_admin()){
    
    // call function for admin menu
    add_action('admin_menu', 'on_google1_options');
    
    // function for google +1 options
    function on_google1_options(){
        
        // add a new setting submenu
        add_options_page('Google +1 Options', 'Google +1 Button', 'manage_options', 'google1-button', 'on_google1_panel');
        
    }
    
    // adding the filter for tinyMCE
    add_filter('mce_external_plugins', "on_google1plugin_register");
    add_filter('mce_buttons', 'on_google1_add_button', 0);
    
    
    /**
     * add separator ont tinyMCE
     *
     * @param array $buttons
     * @return array 
     */
    function on_google1_add_button($buttons)
    {
        array_push($buttons, "separator", "google1plugin");
        return $buttons;
    }

    /**
     * register plugin google1 on tinyMCE
     *
     * @param array $plugin_array
     * @return array 
     */
    function on_google1plugin_register($plugin_array)
    {
        $url    = ON_GOOGLE1_URL."tinymce/editor_plugin.js";

        $plugin_array["google1plugin"] = $url;
        return $plugin_array;
    }
    
} else {

    // add button to content area.
    add_filter('the_content', 'on_google_plusone');
    
    /**
     * adding to content
     *
     * @param string $content
     * @return string 
     */
    function on_google_plusone($content) {
        
        // check condition of page and setting plugin
        if ((is_single() && get_option('on_google1_posts') == 'on') || 
            (is_page() && get_option('on_google1_pages') == 'on') || 
            ((is_home() || is_front_page()) && get_option('on_google1_homepage') == 'on')) {
            
            $size       = (get_option('on_google1_size') == '')     ? 'standard':get_option('on_google1_size');
            $count      = (get_option('on_google1_count') == 'on')  ? '':' count="false"';
            $callback   = (get_option('on_google1_callback') != '') ? ' callback="'.get_option('on_google1_callback').'"':'';
            $top        = (get_option('on_google1_top') == 'on')    ? '<div><g:plusone size="'.$size.'" href="'.get_permalink().'"'.$count.$callback.'></g:plusone></div>':'';
            $bottom     = (get_option('on_google1_bottom') == 'on') ? '<div><g:plusone size="'.$size.'" href="'.get_permalink().'"'.$count.$callback.'></g:plusone></div>':'';

            $content    = $top.$content.$bottom;
        }
            
        return $content;
    }

    // add js script of google+1 to wp_head
    add_action ('wp_head','on_google_plusone_script');    
    function on_google_plusone_script() {
            $language   = (get_option('on_google1_lang') != 'en-US')? "\n{lang: '".get_option('on_google1_lang')."'}\n": '';
            
            echo "<!-- Google +1  -->\n";
            echo '<script type="text/javascript" src="https://apis.google.com/js/plusone.js">'.$language.'</script>';
            echo "\n";
    }
    
    
    // script for display button anywhere.
    function the_google1_advance(){
        
        $size       = (get_option('on_google1_size') == '')     ? 'standard':get_option('on_google1_size');
        $count      = (get_option('on_google1_count') == 'on')  ? '':' count="false"';
        $callback   = (get_option('on_google1_callback') != '') ? ' callback="'.get_option('on_google1_callback').'"':'';
        $button     = '<g:plusone size="'.$size.'" href="'.get_permalink().'"'.$count.$callback.'></g:plusone>';

        echo $button;
    }
    
    /**
     * Function for display the shortcode.
     *
     * @param type $onShortcode
     * @return string 
     */
    function on_google1_shortcode($onShortcode) {
        extract(shortcode_atts(array(
                    "count"     => get_option('on_google1_count', 'on'),
                    "url"       => get_permalink(),
                    "size"      => get_option('on_google1_size', 'standard'),
                    "callback"  => get_option('on_google1_callback', ''),
        ), $onShortcode));
        
        
        $size       = ($size == '')     ? 'standard' : $size;
        $count      = ($count == 'on')  ? '':' count="false"';
        $callback   = ($callback != '') ? ' callback="'.$callback.'"':'';
        
        $output     = "<!-- Google +1 Advance -->\n";        
        $output    .= '<g:plusone size="'.$size.'" href="'.$url.'"'.$count.$callback.'></g:plusone>';
        
        return $output;
    }

    add_filter('widget_text', 'do_shortcode');
    add_shortcode('google1', 'on_google1_shortcode');
        
}


/**
 * Function for display the Plugin Panel Options.
 */
function on_google1_panel() { ?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>Google +1 Options</h2>
    
    <div style="float: right; width: 300px; padding: 5px; background-color: #FFFBCC; border: 1px solid #E6DB55; color: #555;">
        <h3>Thanks a lot</h3>
        <p>Thanks for using my plugin, you can contact me for say hello <a href="http://onnayokheng.com">Onnay Okheng</a> or buy me a cup of chocolate :)</p>
    </div>

    <form method="post" action="options.php" id="options" style="float: left;">
    <?php wp_nonce_field('update-options') ?>
        
        <h3>Setting Buttons</h3>
        
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row">Size</th>
                    <td>
                        
                        <select name="on_google1_size">
                            <option value="standard" <?php if (get_option('on_google1_size') == 'standard') { echo "selected=\"selected\""; } ?>>
                            Standard
                            </option>
                            <option value="small" <?php if (get_option('on_google1_size') == 'small') { echo "selected=\"selected\""; } ?>>
                            Small
                            </option>
                            <option value="medium" <?php if (get_option('on_google1_size') == 'medium') { echo "selected=\"selected\""; } ?>>
                            Medium
                            </option>
                            <option value="tall" <?php if (get_option('on_google1_size') == 'tall') { echo "selected=\"selected\""; } ?>>
                            Tall
                            </option>
                        </select>
                        
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Language</th>
                    <td>                        
                        <select name="on_google1_lang">
                              <option value="ar" <?php if (get_option('on_google1_lang') == 'ar') { echo "selected=\"selected\""; } ?>>
                                Arabic
                              </option>
                              <option value="bg" <?php if (get_option('on_google1_lang') == 'bg') { echo "selected=\"selected\""; } ?>>
                                Bulgarian
                              </option>
                              <option value="ca" <?php if (get_option('on_google1_lang') == 'ca') { echo "selected=\"selected\""; } ?>>
                                Catalan
                              </option>
                              <option value="zh-CN" <?php if (get_option('on_google1_lang') == 'zn-CH') { echo "selected=\"selected\""; } ?>>
                                Chinese (Simplified)
                              </option>
                              <option value="zh-TW" <?php if (get_option('on_google1_lang') == 'zh-TW') { echo "selected=\"selected\""; } ?>>
                                Chinese (Traditional)
                              </option>
                              <option value="hr" <?php if (get_option('on_google1_lang') == 'hr') { echo "selected=\"selected\""; } ?>>
                                Croatian
                              </option>
                              <option value="cs" <?php if (get_option('on_google1_lang') == 'cs') { echo "selected=\"selected\""; } ?>>
                                Czech
                              </option>
                              <option value="da" <?php if (get_option('on_google1_lang') == 'da') { echo "selected=\"selected\""; } ?>>
                                Danish
                              </option>
                              <option value="nl" <?php if (get_option('on_google1_lang') == 'nl') { echo "selected=\"selected\""; } ?>>
                                Dutch
                              </option>
                              <option value="en-US" <?php if (get_option('on_google1_lang') == 'en-US') { echo "selected=\"selected\""; } ?>>
                                English (US)
                              </option>
                              <option value="en-GB" <?php if (get_option('on_google1_lang') == 'en-GB') { echo "selected=\"selected\""; } ?>>
                                English (UK)
                              </option>
                              <option value="et" <?php if (get_option('on_google1_lang') == 'et') { echo "selected=\"selected\""; } ?>>
                                Estonian
                              </option>
                              <option value="fil" <?php if (get_option('on_google1_lang') == 'fil') { echo "selected=\"selected\""; } ?>>
                                Filipino
                              </option>
                              <option value="fi" <?php if (get_option('on_google1_lang') == 'fi') { echo "selected=\"selected\""; } ?>>
                                Finnish
                              </option>
                              <option value="fr" <?php if (get_option('on_google1_lang') == 'fr') { echo "selected=\"selected\""; } ?>>
                                French
                              </option>
                              <option value="de" <?php if (get_option('on_google1_lang') == 'de') { echo "selected=\"selected\""; } ?>>
                                German
                              </option>
                              <option value="el" <?php if (get_option('on_google1_lang') == 'el') { echo "selected=\"selected\""; } ?>>
                                Greek
                              </option>
                              <option value="iw" <?php if (get_option('on_google1_lang') == 'iw') { echo "selected=\"selected\""; } ?>>
                                Hebrew
                              </option>
                              <option value="hi" <?php if (get_option('on_google1_lang') == 'hi') { echo "selected=\"selected\""; } ?>>
                                Hindi
                              </option>
                              <option value="hu" <?php if (get_option('on_google1_lang') == 'hu') { echo "selected=\"selected\""; } ?>>
                                Hungarian
                              </option>
                              <option value="id" <?php if (get_option('on_google1_lang') == 'id') { echo "selected=\"selected\""; } ?>>
                                Indonesian
                              </option>
                              <option value="it" <?php if (get_option('on_google1_lang') == 'it') { echo "selected=\"selected\""; } ?>>
                                Italian
                              </option>
                              <option value="ja" <?php if (get_option('on_google1_lang') == 'ja') { echo "selected=\"selected\""; } ?>>
                                Japanese
                              </option>
                              <option value="ko" <?php if (get_option('on_google1_lang') == 'ko') { echo "selected=\"selected\""; } ?>>
                                Korean
                              </option>
                              <option value="lv" <?php if (get_option('on_google1_lang') == 'lv') { echo "selected=\"selected\""; } ?>>
                                Latvian
                              </option>
                              <option value="lt" <?php if (get_option('on_google1_lang') == 'lt') { echo "selected=\"selected\""; } ?>>
                                Lithuanian
                              </option>
                              <option value="ms" <?php if (get_option('on_google1_lang') == 'ms') { echo "selected=\"selected\""; } ?>>
                                Malay
                              </option>
                              <option value="no" <?php if (get_option('on_google1_lang') == 'no') { echo "selected=\"selected\""; } ?>>
                                Norwegian
                              </option>
                              <option value="fa" <?php if (get_option('on_google1_lang') == 'fa') { echo "selected=\"selected\""; } ?>>
                                Persian
                              </option>
                              <option value="pl" <?php if (get_option('on_google1_lang') == 'pl') { echo "selected=\"selected\""; } ?>>
                                Polish
                              </option>
                              <option value="pt-BR" <?php if (get_option('on_google1_lang') == 'pt-BR') { echo "selected=\"selected\""; } ?>>
                                Portuguese (Brazil)
                              </option>
                              <option value="pt-PT" <?php if (get_option('on_google1_lang') == 'pt-PT') { echo "selected=\"selected\""; } ?>>
                                Portuguese (Portugal)
                              </option>
                              <option value="ro" <?php if (get_option('on_google1_lang') == 'ro') { echo "selected=\"selected\""; } ?>>
                                Romanian
                              </option>
                              <option value="ru" <?php if (get_option('on_google1_lang') == 'ru') { echo "selected=\"selected\""; } ?>>
                                Russian
                              </option>
                              <option value="sr" <?php if (get_option('on_google1_lang') == 'sr') { echo "selected=\"selected\""; } ?>>
                                Serbian
                              </option>
                              <option value="sv" <?php if (get_option('on_google1_lang') == 'sv') { echo "selected=\"selected\""; } ?>>
                                Swedish
                              </option>
                              <option value="sk" <?php if (get_option('on_google1_lang') == 'sk') { echo "selected=\"selected\""; } ?>>
                                Slovak
                              </option>
                              <option value="sl" <?php if (get_option('on_google1_lang') == 'sl') { echo "selected=\"selected\""; } ?>>
                                Slovenian
                              </option>
                              <option value="es" <?php if (get_option('on_google1_lang') == 'es') { echo "selected=\"selected\""; } ?>>
                                Spanish
                              </option>
                              <option value="es-419" <?php if (get_option('on_google1_lang') == 'es-419') { echo "selected=\"selected\""; } ?>>
                                Spanish (Latin America)
                              </option>
                              <option value="th" <?php if (get_option('on_google1_lang') == 'th') { echo "selected=\"selected\""; } ?>>
                                Thai
                              </option>
                              <option value="tr" <?php if (get_option('on_google1_lang') == 'tr') { echo "selected=\"selected\""; } ?>>
                                Turkish
                              </option>
                              <option value="uk" <?php if (get_option('on_google1_lang') == 'uk') { echo "selected=\"selected\""; } ?>>
                                Ukrainian
                              </option>
                              <option value="vi" <?php if (get_option('on_google1_lang') == 'vi') { echo "selected=\"selected\""; } ?>>
                                Vietnamese
                              </option>
                        </select>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Show +1 count in the button</th>
                    <td>                        
                            <?php $checked_count = (get_option('on_google1_count') == 'on') ? ' checked="yes"' : ''; ?>                    
                            <label id="on_google1_count" ><input type="checkbox" id="on_google1_count" name="on_google1_count"<?php echo $checked_count; ?> /> Enabled / Disabled</label>                                    
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Default JS callback Funtion</th>
                    <td>
                            <input type="text" name="on_google1_callback" value="<?php echo get_option('on_google1_callback'); ?>" />
                            <br/>Default is empty.
                    </td>
                </tr>

            </tbody>
        </table>
        <br />
        <h3>Setting General</h3>
        
        <table class="form-table">
            <tbody>

                <tr valign="top">
                    <th scope="row">Display position</th>
                    <td>                        
                            <?php $checked_top = (get_option('on_google1_top') == 'on') ? ' checked="yes"' : ''; ?>                    
                            <label id="on_google1_top" ><input type="checkbox" id="on_google1_top" name="on_google1_top"<?php echo $checked_top; ?> /> Top of content</label>                                    
                            <br />
                            <?php $checked_bottom = (get_option('on_google1_bottom') == 'on') ? ' checked="yes"' : ''; ?>                    
                            <label id="on_google1_bottom" ><input type="checkbox" id="on_google1_bottom" name="on_google1_bottom"<?php echo $checked_bottom; ?> /> Bottom of content</label>                                    
                            
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">Show on </th>
                    <td>
                            <?php $checked_posts = (get_option('on_google1_posts') == 'on') ? ' checked="yes"' : ''; ?>
                            <label id="on_google1_posts" ><input type="checkbox" id="on_google1_posts" name="on_google1_posts"<?php echo $checked_posts; ?> /> Post</label>

                            <br/>

                            <?php $checked_pages = (get_option('on_google1_pages') == 'on') ? ' checked="yes"' : ''; ?>
                            <label id="on_google1_pages" ><input type="checkbox" id="on_google1_pages" name="on_google1_pages"<?php echo $checked_pages; ?> /> Pages</label>

                            <br/>

                            <?php $checked_home = (get_option('on_google1_homepage') == 'on') ? ' checked="yes"' : ''; ?>
                            <label id="on_google1_homepage" ><input type="checkbox" id="on_google1_homepage" name="on_google1_homepage"<?php echo $checked_home; ?> /> Home Page</label>
                    </td>
                </tr>

            </tbody>
        </table>
        
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="on_google1_size, on_google1_callback, on_google1_lang, on_google1_posts, on_google1_pages, on_google1_homepage, on_google1_count, on_google1_top, on_google1_bottom" />
        <div class="submit"><input type="submit" class="button-primary" name="submit" value="Save Settings"></div>


    </form>

</div>

<?php } ?>