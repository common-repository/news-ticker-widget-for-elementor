<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Elementor_NT_Widget extends Widget_Base {	
	public function get_name() { 		//Function for get the slug of the element name.
		return 'elementor-news-ticker';
	}
	public function get_title() { 		//Function for get the name of the element.
		return __( 'News Ticker', ELEMENTORNEWTICKER_DOMAIN );
	}	
	public function get_icon() { 		//Function for get the icon of the element.
		return ' eicon-form-vertical';
	}	
	public function get_categories() { 		//Function for include element into the category.
		return [ 'ele-nt' ];
	}
	public function newticker_get_post_types()
    {
        $post_types = get_post_types(['public' => true, 'show_in_nav_menus' => true], 'objects');
        $post_types = wp_list_pluck($post_types, 'label', 'name');
        return array_diff_key($post_types, ['elementor_library', 'attachment']);
    }
	/**
	 * Retrieve News Ticker widget link URL.
	 *
	 * @access private
	 *
	 * @param object $instance
	 *
	 * @return array | string | false An array/string containing the link URL, or false if no link.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'General Settings', ELEMENTORNEWTICKER_DOMAIN ),
			]
		);		
		$post_types1 = $this->newticker_get_post_types();		
		$taxonomies = get_taxonomies([], 'objects');
		$this->add_control(
            'post_type',
            [
                'label' => __('Source', ELEMENTORNEWTICKER_DOMAIN),
                'type' => Controls_Manager::SELECT,
                'options' => $post_types1,
                'default' => key($post_types1),
            ]
        );
		
		
		foreach ($taxonomies as $taxonomy => $object) {
           if (!isset($object->object_type[0]) || !in_array($object->object_type[0], array_keys($post_types1))) {
                continue;
            }

            $this->add_control(
                $taxonomy . '_ids',
                [
                   'label' => $object->label,
                    'type' => Controls_Manager::SELECT2,
                    'label_block' => true,
                    'multiple' => true,
                    'object_type' => $taxonomy,
                    'options' => wp_list_pluck(get_terms($taxonomy), 'name', 'term_id'),
                    'condition' => [
                        'post_type' => $object->object_type,
                    ],
                ]
            );
        }
		
		
		
		$this->add_control(
			'no_of_post',
			[
				'label' => __( 'Post Number', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::NUMBER,
				'default' => __( '6', ELEMENTORNEWTICKER_DOMAIN )
			]
		);
		$this->add_control(
			'label',
			[
				'label' => __( 'Show label', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', ELEMENTORNEWTICKER_DOMAIN ),
				'label_on' => __( 'On', ELEMENTORNEWTICKER_DOMAIN ),
				'default' => 'yes',
			]
		);
		$this->add_control(
			'label_heading',			[
				'label' => __( 'Label', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Latest News', ELEMENTORNEWTICKER_DOMAIN),
				'placeholder' => __( 'Latest News', ELEMENTORNEWTICKER_DOMAIN ),
				'condition' => [
					'label' => 'yes',
				],
			]
		);		
		 $this->add_control(
            'label_icon',
			[
				'label' => __('Icon', ELEMENTORNEWTICKER_DOMAIN),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fa fa-bolt',
					'library' => 'fa-solid',
				],
            ]
        );
        $this->end_controls_section();
		$this->start_controls_section(
			'sep_content',
			[
				'label' => __( 'Separator', ELEMENTORNEWTICKER_DOMAIN ),
			]
		);
		$this->add_control(
			'sep_type',
			[
				'label' => __( 'Separator Type', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'icon' => __( 'Icon', ELEMENTORNEWTICKER_DOMAIN ),
					'text' => __( 'Text', ELEMENTORNEWTICKER_DOMAIN ),
					'fimage' => __( 'Feature Image', ELEMENTORNEWTICKER_DOMAIN ),
					'pdate' => __( 'Date', ELEMENTORNEWTICKER_DOMAIN ),
					
				],
				'default' => 'icon',
			]
		);	
		$this->add_control(//Add control to select an icon for button1.
            'sep_icon',
			[
				'label' => __('Icon', ELEMENTORNEWTICKER_DOMAIN),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fa fa-circle',
					'library' => 'fa-solid',
				],
            ]
        );
		$this->add_control(
			'sep_text',
			[
				'label' => __( 'Text', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '|', ELEMENTORNEWTICKER_DOMAIN),
				'placeholder' => __( 'Text', ELEMENTORNEWTICKER_DOMAIN ),
				'condition' => [
					'sep_type' => 'text',
				],
			]
		);
        $this->end_controls_section();
		$this->start_controls_section(
			'animation_style',
			[
				'label' => __('Animation', ELEMENTORNEWTICKER_DOMAIN ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_control(
			'animation_speed',
			[
				'label' => __( 'Animation Speed', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::NUMBER,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( '50', ELEMENTORNEWTICKER_DOMAIN),	
			]
		);
		  $this->add_control(
			'nt_background_color',
			[
				'label' => __( 'Background Color', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'default' => '#f1f1f1',
				'selectors' => [
					'{{WRAPPER}} .fd-elementor-news-ticker' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'label_style',
			[
				'label' => __( 'Label', ELEMENTORNEWTICKER_DOMAIN ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'label' => 'yes',
				],
			]
		);
        $this->add_control(
			'label_icon_indent',
			[
				'label' => __( 'Icon Spacing', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .news-ticker-label i' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'label_color',
			[
				'label' => __( 'Label Color', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
				],
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .news-ticker-label' => 'color: {{VALUE}};',
				],
			]
		);
        $this->add_control(
			'label_background_color',
			[
				'label' => __( 'Background Color', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'default' => '#595959',
				'selectors' => [
					'{{WRAPPER}} .news-ticker-label' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .news-ticker-label',
			]
		);		
		$this->end_controls_section();
		$this->start_controls_section(
			'title_style',
			[
				'label' => __( 'Title', ELEMENTORNEWTICKER_DOMAIN ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control('title_padding',
                [
                    'label'         => esc_html__('Padding', ELEMENTORNEWTICKER_DOMAIN),
                    'type'          => Controls_Manager::DIMENSIONS,
                    'size_units'    => [ 'px', 'em', '%' ],
                    'default'       => [
                        'unit'  => 'px',
                        'top'   => 0,    
                        'right' => 20,
                        'bottom'=> 0,
                        'left'  => 20,
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .top-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                        ],
                    ]
                );
        
		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .top-heading' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .top-heading',
			]
		);
		$this->end_controls_section();	
		
		$this->start_controls_section(
			'icon_style',
			[
				'label' => __( 'Icon Separator', ELEMENTORNEWTICKER_DOMAIN ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'sep_type' => 'icon',
				],
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .fd-elementor-news-ticker i' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .fd-elementor-news-ticker i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
        $this->start_controls_section(
			'sep_text_style',
			[
				'label' => __( 'Text Separator', ELEMENTORNEWTICKER_DOMAIN ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'sep_type' => 'text',
				],
			]
		);
		$this->add_control(
			'sep_text_color',
			[
				'label' => __( 'Text Color', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .sep_text' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'sep_text_bg_color',
			[
				'label' => __( 'Background Color', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'default' => '#595959',
				'selectors' => [
					'{{WRAPPER}} .sep_text' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sep_text_typography',
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .sep_text',
			]
		);
		$this->end_controls_section();
		
        $this->start_controls_section(
			'sep_date_style',
			[
				'label' => __( 'Date Separator', ELEMENTORNEWTICKER_DOMAIN ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'sep_type' => 'pdate',
				],
			]
		);
		$this->add_control(
			'sep_date_color',
			[
				'label' => __( 'Color', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .sep_date' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'sep_date_bg_color',
			[
				'label' => __( 'Background Color', ELEMENTORNEWTICKER_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'default' => '#595959',
				'selectors' => [
					'{{WRAPPER}} .sep_date' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sep_date_typography',
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .sep_date',
			]
		);
		$this->end_controls_section();			
	}
	
	/**
	 * Render News Ticker widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	 public function newticker_get_query_args($settings = [])
    {
        $settings = wp_parse_args($settings, [
            'post_type' => 'post',           
            'orderby' => 'date',
            'order' => 'desc',
            'posts_per_page' => 6,
            
        ]);

        $args = [           
            'ignore_sticky_posts' => 1,
            'post_status' => 'publish',
            'posts_per_page' => $settings['no_of_post'],
            
        ];
       
		$args['post_type'] = $settings['post_type'];

		if ($args['post_type'] !== 'page') {
			$args['tax_query'] = [];
			$taxonomies = get_object_taxonomies($settings['post_type'], 'objects');

			foreach ($taxonomies as $object) {
				$setting_key = $object->name . '_ids';

				if (!empty($settings[$setting_key])) {
					$args['tax_query'][] = [
						'taxonomy' => $object->name,
						'field' => 'term_id',
						'terms' => $settings[$setting_key],
					];
				}
			}

			if (!empty($args['tax_query'])) {
				$args['tax_query']['relation'] = 'AND';
			}
		}
        
        

        return $args;
    }
	protected function render() {		
		$settings = $this->get_settings();		
		global $post;       
		$args = $this->newticker_get_query_args($settings);
        $myposts = get_posts( $args );
		// Check that we have query results.
		
		
		if ($myposts) {			?>		<div class="news-ticker-wrap">			<?php
			if(!empty($settings['label_heading']) && $settings['label']=='yes') { ?>
				<div class="news-ticker-label">
				   <span class="news-ticker-icon">
						<?php if (!empty($settings['label_icon'])) : ?>                        
							   <?php Icons_Manager::render_icon( $settings['label_icon'], [ 'aria-hidden' => 'true' ] ); ?>                     
						<?php endif; ?>
					</span>	
				   <?php echo $settings['label_heading'] ?>
				</div>
	        <?php } ?> 
			<div class="fd-elementor-news-ticker" id="fd-ticker-<?php echo $this->get_id(); ?>" data-speed="<?php echo $settings['animation_speed'];?>">
			<?php
				foreach( $myposts as $post ) : 
				//setup_postdata($post);  
				   if($settings['sep_type']=='fimage') { ?>
				        <span class="news-item-<?php echo $this->get_id();?> feature-image"> <?php echo the_post_thumbnail( array( 35,35 ) ); ?></span>
				   <?php } ?>
					<a  class="top-heading news-item-<?php echo $this->get_id(); ?>" href="<?php echo get_permalink(); ?>"> <?php echo get_the_title(); ?></a>
		            <?php if (!empty ( $settings['sep_icon'] ) && $settings['sep_type']=='icon') {  ?>
						<span class="news-item-<?php echo $this->get_id(); ?> sep_icon">
						 <?php Icons_Manager::render_icon( $settings['sep_icon'], [ 'aria-hidden' => 'true' ]);   ?>	
						</span>                      	
					<?php } if(!empty ( $settings['sep_text'] ) && $settings['sep_type']=='text'){ ?>
						<span class="news-item-<?php echo $this->get_id(); ?> sep_text"><?php echo $settings['sep_text'] ; ?></span>
				    <?php 
				    }  if ($settings['sep_type']=='pdate') { ?>
					<span class="news-item-<?php echo $this->get_id(); ?> sep_date"><?php echo get_the_date(); ?></span>
					<?php	
					}
				  endforeach; wp_reset_postdata(); ?>
			</div>			
        </div>
        <?php } ?>
		<script>
			jQuery(document).ready(function($) {
				jQuery("#fd-ticker-<?php echo $this->get_id(); ?>").ticker({
					speed:<?php echo $settings['animation_speed'];?>,
					pauseOnHover:!0,
					item:".news-item-<?php echo $this->get_id(); ?>"
				}).data("ticker");
			}); 
		</script>
		<?php
	}
    /**
	 * Define our Content template settings.
	 */
	protected function content_template() {
	} 	
}
Plugin::instance()->widgets_manager->register( new Elementor_NT_Widget() );