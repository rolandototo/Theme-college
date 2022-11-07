<?php

/*
    Plugin Name: WordCount-post
    Description: Plugin is use to count total words, charters and the time that takes reads all in the single page(Posts)
    Version: 1.0
    Author: Rolandototo.dev
    Author: www.rolandototo.dev
*/

class WordCountAndTimePLugin
{

    function __construct()
    {
        add_action('admin_menu', array($this, 'adminPage'));
        add_action('admin_init', array($this, 'settings'));
        add_filter('the_content', array($this, 'ifWrap'));
    }

    function ifWrap($content){
        if((is_main_query() AND is_single()) AND
        (
            get_option('wcp_wordcount', '1') OR
            get_option('wcp_chartercount', '1') OR
            get_option('wcp_readtime', '1')
        )) {
            return $this->createHTML($content);
        }
        return $content;
    }

    function createHTML($content){
        $html = '<h3>'. esc_html(get_option('wcp_headline', 'Post Stadistics')) .'</h3><p>';

        // Get word count once becauce both wordcount and read time will need it.

        if(get_option('wcp_wordcount', '1') OR get_option('wcp_readtime', '1')){
            $wordCount = str_word_count(strip_tags($content));
        }

        if(get_option('wcp_wordcount', '1')){
            $html .= 'This post has ' . $wordCount . 'words.</br>';
        }

        if(get_option('wcp_chartercount', '1')){
            $html .= 'This post has '. strlen(strip_tags($content)) . ' charaters.<br>';
        }

        if(get_option('wcp_readtime', '1')){
            $html .= 'This post will take about ' . round($wordCount/225) . 'minute(s) to read';
        }

        $html .= '</p>';

        if(get_option('wcp_location', '0') == '0'){
            return $html .$content;
        }
        return $content . $html;

    }


    function settings()
    {
        add_settings_section('wcp_first_section', null, null, 'word-count-setting-page');
        /*Location*/
        add_settings_field('wcp_location', 'Display Location', array($this, 'locationHTML'), 'word-count-setting-page', 'wcp_first_section');
        register_setting('wordcountplugin', 'wcp_location', array('sanitize_callback' => array($this, 'sanitizeLocation'), 'default' => '0'));
        /*headline*/
        add_settings_field('wcp_headline', 'Headline Text', array($this, 'headlineHTML'), 'word-count-setting-page', 'wcp_first_section');
        register_setting('wordcountplugin', 'wcp_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Stadistic'));
        /*Word Count*/
        add_settings_field('wcp_wordcount', 'Word Count', array($this, 'checkboxHTML'), 'word-count-setting-page', 'wcp_first_section', array('TheName'=> 'wcp_wordcount'));
        register_setting('wordcountplugin', 'wcp_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
        /*Charter count*/
        add_settings_field('wcp_chartercount', 'Charter count', array($this, 'checkboxHTML'), 'word-count-setting-page', 'wcp_first_section', array('TheName'=> 'wcp_chartercount'));
        register_setting('wordcountplugin', 'wcp_chartercount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
        /*Read Time*/
        add_settings_field('wcp_readtime', 'Read Time', array($this, 'checkboxHTML'), 'word-count-setting-page', 'wcp_first_section', array('TheName'=> 'wcp_readtime'));
        register_setting('wordcountplugin', 'wcp_readtime', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
    }

    function sanitizeLocation($input){
        if($input != '0' AND $input != '1'){
            add_settings_error('wcp_location', 'wcp_location_error', 'Display location must be either beginnig or end');
            return get_option('wcp_location');
        }
        return $input;
    }



    function locationHTML()
    { ?>
        <select name="wcp_location">
            <option value="0" <?php selected(get_option('wcp_location'), '0'); ?>>Beginnig of Post</option>
            <option value="1" <?php selected(get_option('wcp_location'), '1'); ?>>End of Post</option>
        </select>
    <?php }

    function headlineHTML()
    { ?>
        <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')) ?>">
    <?php }

    function checkboxHTML($arg)
    { ?>
        <input type="checkbox" name="<?php echo $arg['TheName'] ?>" value="1" <?php checked(get_option($arg['TheName']), '1') ?>>
    <?php }

    function adminPage()
    {
        add_options_page('Word Count Settings', 'Word Count', 'manage_options', 'word-count-setting-page', array($this, 'ourHTML'));
    }

    function ourHTML()
    { ?>
        <div class="wrap">
            <h1>Word Count Setting</h1>
            <form action="options.php" method="POST">
                <?php
                settings_fields('wordcountplugin');
                do_settings_sections('word-count-setting-page');
                submit_button();
                ?>
            </form>
        </div>

<?php }
}

$wordCountAndTimePlugin = new WordCountAndTimePLugin();
