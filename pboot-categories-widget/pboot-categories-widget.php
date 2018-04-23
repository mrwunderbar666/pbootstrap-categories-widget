<?php
/*
Plugin Name: PBootstrap Categories Widget
Plugin URI: https://github.com/mrwunderbar666/
Description: Widget to display categories. Made for bootstrap 4. Based on WP Categories Widget by MR Web Solution http://www.mrwebsolution.in/ (http://raghunathgurjar.wordpress.com)
Author: MrWunderbar
Author URI: www.balluff-transnational.eu
Version: 0.9
*/

/*  Copyright 2016-17  wp-categories-widget  (email : raghunath.0087@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**************************************************************
                START CLASSS PBootCategoriesWidget
**************************************************************/
class PBootCategoriesWidget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'pboot_categories_widget', // Base ID
			__( 'PBoot Categories list', 'mrwunderbar' ), // Name
			array( 'description' => esc_html__( 'The Taxonomy Category Widget', 'mrwunderbar' ), ) // Args
		);
		if(!is_admin())
		add_action('wcw_style',array($this,'wcw_style_func'));
		add_filter( "plugin_action_links_".plugin_basename( __FILE__ ), array(&$this,'wcw_add_settings_link') );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		$va_category_HTML ='<div class="">';
		if ( ! empty( $instance['wcw_title'] ) && !$instance['wcw_hide_title']) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['wcw_title'] ) . $args['after_title'];
		}
		// add css
		do_action('wcw_style','wcw_style_func');
		/** return category list */
		if($instance['wcw_taxonomy_type']){
			$va_category_HTML .='<div class="list-group">';
				$args_val = array( 'hide_empty=0' );
				$terms = get_terms( $instance['wcw_taxonomy_type'], $args_val );
				if ( $terms ) {
				$excludeCat= $instance['wcw_exclude_categories'] ? $instance['wcw_exclude_categories'] : '';
					foreach ( $terms as $term ) {

						$term_link = get_term_link( $term );
						if($excludeCat!='' && in_array($term->term_id,$excludeCat))
						{
							continue;
							}

						if ( is_wp_error( $term_link ) ) {
						continue;
						}

					$currentActiveClass='';

					if($term->taxonomy=='category' && is_category())
					{
					  $thisCat = get_category(get_query_var('cat'),false);
					  if($thisCat->term_id == $term->term_id)
						$currentActiveClass='active';
				    }

					if(is_tax())
					{
					    $currentTermType = get_query_var( 'taxonomy' );
					    $termId= get_queried_object()->term_id;
						 if(is_tax($currentTermType) && $termId==$term->term_id)
						  $currentActiveClass='active';
					}

						$va_category_HTML .='<a class="'.$currentActiveClass.' list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="' . esc_url( $term_link ) . '">' . $term->name . '';
						if (empty( $instance['wcw_hide_count'] )) {
						$va_category_HTML .='<span class="badge badge-light badge-pill">'.$term->count.'</span>';
						}
						$va_category_HTML .='</a>';
					}
				}
			$va_category_HTML .='</div>';
			echo $va_category_HTML;
			}
		$va_category_HTML .='</div>';
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$wcw_title 					= ! empty( $instance['wcw_title'] ) ? $instance['wcw_title'] : esc_html__( 'WP Categories', 'virtualemployee' );
		$wcw_hide_title 			= ! empty( $instance['wcw_hide_title'] ) ? $instance['wcw_hide_title'] : esc_html__( '', 'virtualemployee' );
		$wcw_taxonomy_type 			= ! empty( $instance['wcw_taxonomy_type'] ) ? $instance['wcw_taxonomy_type'] : esc_html__( '', 'virtualemployee' );
		$wcw_exclude_categories 	= ! empty( $instance['wcw_exclude_categories'] ) ? $instance['wcw_exclude_categories'] : esc_html__( '', 'virtualemployee' );
		$wcw_hide_count 			= ! empty( $instance['wcw_hide_count'] ) ? $instance['wcw_hide_count'] : esc_html__( '', 'virtualemployee' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'wcw_title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wcw_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wcw_title' ) ); ?>" type="text" value="<?php echo esc_attr( $wcw_title ); ?>">
		</p>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wcw_hide_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wcw_hide_title' ) ); ?>" type="checkbox" value="1" <?php checked( $wcw_hide_title, 1 ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id( 'wcw_hide_title' ) ); ?>"><?php _e( esc_attr( 'Hide Title' ) ); ?> </label>
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'wcw_taxonomy_type' ) ); ?>"><?php _e( esc_attr( 'Taxonomy Type:' ) ); ?></label>
		<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wcw_taxonomy_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wcw_taxonomy_type' ) ); ?>">
					<?php
					$args = array(
					  'public'   => true,
					  '_builtin' => false

					);
					$output = 'names'; // or objects
					$operator = 'and'; // 'and' or 'or'
					$taxonomies = get_taxonomies( $args, $output, $operator );
					array_push($taxonomies,'category');
					if ( $taxonomies ) {
					foreach ( $taxonomies as $taxonomy ) {

						echo '<option value="'.$taxonomy.'" '.selected($taxonomy,$wcw_taxonomy_type).'>'.$taxonomy.'</option>';
					}
					}

				?>
		</select>
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'wcw_exclude_categories' ) ); ?>"><?php _e( esc_attr( 'Exclude Category:' ) ); ?></label>
		<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wcw_exclude_categories' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wcw_exclude_categories' ) ); ?>[]" multiple>
					<?php
					if($wcw_taxonomy_type){
					$args = array( 'hide_empty=0' );
					$terms = get_terms( $wcw_taxonomy_type, $args );
			        echo '<option value="" '.selected(true, in_array('',$wcw_exclude_categories), false).'>None</option>';
					if ( $terms ) {
					foreach ( $terms as $term ) {
						echo '<option value="'.$term->term_id.'" '.selected(true, in_array($term->term_id,$wcw_exclude_categories), false).'>'.$term->name.'</option>';
					}

					}
				}

				?>
		</select>
		</p>
		<p>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'wcw_hide_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wcw_hide_count' ) ); ?>" type="checkbox" value="1" <?php checked( $wcw_hide_count, 1 ); ?>>
		<label for="<?php echo esc_attr( $this->get_field_id( 'wcw_hide_count' ) ); ?>"><?php _e( esc_attr( 'Hide Count' ) ); ?> </label>
		</p>
		<p><a href="https://github.com/mrwunderbar666/">Contact to Author</a></p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['wcw_title'] 					= ( ! empty( $new_instance['wcw_title'] ) ) ? strip_tags( $new_instance['wcw_title'] ) : '';
		$instance['wcw_hide_title'] 			= ( ! empty( $new_instance['wcw_hide_title'] ) ) ? strip_tags( $new_instance['wcw_hide_title'] ) : '';
		$instance['wcw_taxonomy_type'] 			= ( ! empty( $new_instance['wcw_taxonomy_type'] ) ) ? strip_tags( $new_instance['wcw_taxonomy_type'] ) : '';
		$instance['wcw_exclude_categories'] 	= ( ! empty( $new_instance['wcw_exclude_categories'] ) ) ? $new_instance['wcw_exclude_categories'] : '';
		$instance['wcw_hide_count'] 			= ( ! empty( $new_instance['wcw_hide_count'] ) ) ? strip_tags( $new_instance['wcw_hide_count'] ) : '';
		return $instance;
	}
	/** plugin CSS **/
	function wcw_style_func_css()
	{
		$style='';
	echo $style;
	}
	function wcw_style_func()
	{
		add_action('wp_footer',array($this,'wcw_style_func_css'));
	}
	/** updtate plugins links using hooks**/
	// Add settings link to plugin list page in admin
	function wcw_add_settings_link( $links ) {
		$settings_link = '<a href="widgets.php">' . __( 'Settings Widget', 'mrwunderbar' ) . '</a> | <a href="https://github.com/mrwunderbar666/">' . __( 'Contact to Author', 'mrwunderbar' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
}// class PBootCategoriesWidget

// register PBootCategoriesWidget widget
function register_pboot_categories_widget() {
    register_widget( 'PBootCategoriesWidget' );
}
add_action( 'widgets_init', 'register_pboot_categories_widget');
/**************************************************************
                END CLASSS PBootCategoriesWidget
**************************************************************/
