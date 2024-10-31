<?php
namespace Elementor;   
// Create Widgets category into elementor.
  
function ele_news_ticker_widgets_init(){
    Plugin::instance()->elements_manager->add_category(
        'ele-nt',
        [
            'title'  => 'Flickdevs',
            'icon' => 'font'
        ],
        1
    );
}
add_action('elementor/init','Elementor\ele_news_ticker_widgets_init');
?>