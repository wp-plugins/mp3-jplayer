<?php
/**
*	SHORTCODES WIDGET
*	add players via shortcodes. 
*	---
*/

if ( class_exists("WP_Widget") ) { 
	if ( !class_exists("MP3j_single") ) {
	
		class MP3j_single extends WP_Widget {
		
			/* Constructor (required by api) */
			function MP3j_single() {
				$widget_ops = array( 
					'classname' => 'mp3jplayerwidget2', 
					'description' => __('Add audio players by writing shortcodes.', 
					'mp3jplayerwidget2') 
				);
				$control_ops = array( 
					'id_base' => 'mp3mi-widget'
				);
				$this->WP_Widget( 'mp3mi-widget', __('MP3-jPlayer|Shortcodes', 'mp3jplayerwidget2'), $widget_ops, $control_ops );
			}
	
	
			/* Runs the shortcodes and writes the players (required by api) */
			function widget( $args, $instance )
			{
				if ( !is_home() && !is_archive() && !is_singular() && !is_search() ) { 
					return;
				}
				
				global $MP3JP;
				if ( $MP3JP->page_filter( $instance['restrict_list'], $instance['restrict_mode'] ) ) { 
					return; 
				}
				
				$MP3JP->Caller = "widget";
				
				$arb_text = $MP3JP->strip_scripts( $instance['arb_text'] );
				
				$shortcodes_return = do_shortcode( $arb_text );
				$MP3JP->Caller = false;
				
				if ( '' !== $shortcodes_return )
				{
					extract( $args ); // supplied WP theme vars 
					echo $before_widget;
					if ( $instance['title'] ) { 
						echo $before_title . $MP3JP->strip_scripts( $instance['title'] ) . $after_title; 
					}
					echo $shortcodes_return;
					echo $after_widget;
				}
				return;
			}
	   
	   
			/* Updates the widget settings (required by api) */
			function update( $new_instance, $old_instance ) {
				
				global $MP3JP;
				
				$instance = $old_instance;
				$instance['title'] = $MP3JP->strip_scripts( $new_instance['title'] );
				$instance['restrict_list'] = $MP3JP->strip_scripts( $new_instance['restrict_list'] );
				$instance['restrict_mode'] = $MP3JP->strip_scripts( $new_instance['restrict_mode'] );
				$instance['arb_text'] = $MP3JP->strip_scripts( $new_instance['arb_text'] );
				return $instance;
			}

			
			/* Creates defaults and writes widget panel (required by api) */
			function form( $instance ) {
				
				global $MP3JP;
				
				$defaultvalues = array(
					'title' => '',
					'restrict_list' => '',
					'restrict_mode' => 'exclude',
					'arb_text' => ''
				);
				$instance = wp_parse_args( (array) $instance, $defaultvalues );
				?>
					<h3>Shortcodes:</h3>
					<textarea class="widefat" style="font-size:11px;" rows="8" cols="85" id="<?php echo $this->get_field_id( 'arb_text' ); ?>" name="<?php echo $this->get_field_name( 'arb_text' ); ?>"><?php echo $MP3JP->strip_scripts( $instance['arb_text'] ); ?></textarea>
					
					<br><h3>Widget Heading:</h3>
					<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $MP3JP->strip_scripts( $instance['title'] ); ?>" />
					
					<br><h3>Page Filter:</h3>
					<p style="line-height:200%; margin-top:-10px;"><strong>Include</strong>
						<input type="radio" id="<?php echo $this->get_field_id( 'restrict_mode' ); ?>" name="<?php echo $this->get_field_name( 'restrict_mode' ); ?>" value="include" <?php if ($instance['restrict_mode'] == "include") { _e('checked="checked"', "mp3jplayerwidget2"); }?> />
						or&nbsp;
						<input type="radio" id="<?php echo $this->get_field_id( 'restrict_mode' ); ?>" name="<?php echo $this->get_field_name( 'restrict_mode' ); ?>" value="exclude" <?php if ($instance['restrict_mode'] == "exclude") { _e('checked="checked"', "mp3jplayerwidget2"); }?> />
						<strong>Exclude</strong> &nbsp; 
						<input type="text" class="widefat" style="font-size:11px; width:200px;" id="<?php echo $this->get_field_id( 'restrict_list' ); ?>" name="<?php echo $this->get_field_name( 'restrict_list' ); ?>" value="<?php echo $MP3JP->strip_scripts( $instance['restrict_list'] ); ?>" />
					</p>
					<p style="line-height:140%; margin-top:-8px; margin-bottom:20px;"><span>A comma separated list, it can contain <code>index</code>, <code>archive</code>, <code>post</code>, <code>search</code>, and any <strong>post IDs</strong>.</span></p>	
						
					
					
					<hr/><br>
				<?php	
			}
		} //close class
	}	
}
?>