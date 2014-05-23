<?php 
/*
Plugin Name: Crossover Widget
Plugin URI: http://www.brandensilva.com
Description: Display a players Dribbble shots.
Version: 0.9.0
Author: Branden Silva
Author URI: http://www.brandensilva.com
Licence: MIT
*/

function crossover_scripts() {
	if( !is_admin() ) {

		wp_enqueue_script('jquery');
		wp_enqueue_script('jribbble', plugins_url( '/jribbble/jribbble-1.0.1.min.js' , __FILE__ ), array( 'jquery' ));
	}
}

add_action('wp_enqueue_scripts', 'crossover_scripts');


class Crossover_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'crossover_widget', // Base ID
			__('Crossover', 'text_domain'), // Name
			array( 'description' => __( 'Display a players Dribbble shots', 'text_domain' ), ) // Args
		);
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
		$player = esc_attr( $instance['player'] );
		$size = esc_attr( $instance['size'] );
		$size = explode('x', $size);

		$shot_count  = ( isset($instance[ 'shot_count' ] ) && ( !	empty($instance[ 'shot_count' ] ) ) && (	is_numeric($instance[	'shot_count'	])) ) ? 
		floor(esc_attr( $instance['shot_count'] )) : '1';

		echo $args['before_widget'];
		if ( !empty( $player ) )

?>
		<div class="crossover"></div>
		<script>
        var callback = function (playerShots) {
            var html = '';

            $.each(playerShots.shots, function (i, shot) {
                html += '<a class="shot" href="' + shot.url + '">';
                html += '<img src="' + shot.image_url + '" width="<?php echo esc_attr($size[0]); ?>" height="<?php echo esc_attr($size[1]); ?>" ';
                html += 'alt="' + shot.title + '"></a></li>';
            });

            $('.crossover').html(html);

        };

        $.jribbble.getShotsByPlayerId('<?php echo esc_attr( $player ); ?>', callback, {page: 1, per_page: <?php echo $shot_count ?> });
    </script>

<?php
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
		if ( isset( $instance[ 'player' ] ) ) {
			$player = $instance[ 'player' ];
		}
		else {
			$player = __( 'simplebits', 'text_domain' );
		}

		if ( isset( $instance[ 'size' ] ) ) {
			$size = esc_attr( $instance[ 'size' ] );
		}

		if ( empty( $size ) ) {
			$size = __('400x300');
		}

		if ( isset($instance[ 'shot_count' ]) ) {
			$shot_count = floor(esc_attr($instance[ 'shot_count' ]));
		}

		if ( (empty( $shot_count )) || (!is_numeric($shot_count)) ) {
			$shot_count = __('1');
		}


		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'player' ); ?>"><?php _e( 'Player Name:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'player' ); ?>" name="<?php echo $this->get_field_name( 'player' ); ?>" type="text" value="<?php echo esc_attr( $player ); ?>">
		</p>
		<p>
		 <label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e('Image Size (width x height):' ); ?>
			 <select class="widefat" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>">
				 <option value="100x75" <?php selected('100x75', $size); ?>><?php _e('100x75');?></option>
				 <option value="200x150" <?php selected('200x150', $size); ?>><?php _e('200x150');?></option>
				 <option value="400x300" <?php selected('400x300', $size); ?>><?php _e('400x300');?></option>
			 </select>
		 </label>			
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'shot_count' ); ?>"><?php _e( 'Shot Count:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'shot_count' ); ?>" name="<?php echo $this->get_field_name( 'shot_count' ); ?>" type="text" value="<?php echo esc_attr( $shot_count ); ?>">			
		</p>
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

		$instance[ 'player' ] 		= ( ! empty( $new_instance[ 'player' ] ) ) 	? strip_tags( $new_instance[ 'player' ] ) : 'simplebits';
		$instance[ 'size' ] 			= ( ! empty( $new_instance[ 'size' ] ) )	? strip_tags( $new_instance[ 'size' ] ) : '400x300';
		$instance[ 'shot_count' ] = ( ! empty( $new_instance[ 'shot_count' ] ) && ( is_numeric( $new_instance[ 'shot_count' ]) ) ) ? floor(strip_tags( $new_instance[ 'shot_count' ] )) : '1';

		return $instance;
	}

} // Class Crossover_Widget

// Register Crossover_Widget
function register_crossover_widget() {
    register_widget( 'Crossover_Widget' );
}

add_action( 'widgets_init', 'register_crossover_widget' );

?>