<?php

function uni_file()
{

    wp_enqueue_script('main-uni-js', get_theme_file_uri('/js/scripts-bundle.js'), null, '1.0', true);

    wp_enqueue_style('custome-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awosome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('uni_normalize_style', get_template_directory_uri() . '/index.css');
    wp_enqueue_style('uni_main_style', get_stylesheet_uri());
}

add_action('wp_enqueue_scripts', 'uni_file');

function uni_features()
{
    add_theme_support('title-tag');
}

add_action('after_setup_theme', 'uni_features');


function university_adjust_queries($query)
{
    if (!is_admin() and is_post_type_archive('program') and $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('post-per-page', '-1');
    }

    if (!is_admin() and is_post_type_archive('event') and $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric'
            )
        ));
    }
}

add_action('pre_get_posts', 'university_adjust_queries');
