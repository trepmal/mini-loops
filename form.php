<?php if ( ! defined( 'ABSPATH' ) ) die( '-1' ); ?>
		<p style="width:40%;float:left;">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mini-loops' );?>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo stripslashes( $title ); ?>" />
			</label>
		</p>
		<p style="width:40%;margin-left:2%;float:left;">
			<label for="<?php echo $this->get_field_id( 'title_url' ); ?>"><?php _e( 'Title URL:', 'mini-loops' );?>
				<input class="widefat" id="<?php echo $this->get_field_id('title_url'); ?>" name="<?php echo $this->get_field_name('title_url'); ?>" type="text" value="<?php echo $title_url; ?>" />
			</label>
		</p>
		<p style="width:15%;margin-left:2%;float:left;padding-top:20px;height:20px;">
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hide_title'); ?>" name="<?php echo $this->get_field_name('hide_title'); ?>"<?php checked( $hide_title ); ?> />
			<label for="<?php echo $this->get_field_id('hide_title'); ?>"><?php _e('Hide Title?', 'mini-loops' );?></label>
		</p>

		<hr style="clear:both;"/>

	<div style="width:48%;float:left;">

		<h3><?php _e( 'Query', 'mini-loops' ); ?></h3>
		<p style="width:48%;float:left;">
			<label for="<?php echo $this->get_field_id('number_posts'); ?>"><?php _e('Number of Posts:', 'mini-loops' );?>
				<input class="widefat" id="<?php echo $this->get_field_id('number_posts'); ?>" name="<?php echo $this->get_field_name('number_posts'); ?>" type="number" value="<?php echo $number_posts; ?>" />
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo $this->get_field_id('post_offset'); ?>"><?php _e('Posts Offset:', 'mini-loops' );?>
				<input class="widefat" id="<?php echo $this->get_field_id('post_offset'); ?>" name="<?php echo $this->get_field_name('post_offset'); ?>" type="number" value="<?php echo $post_offset; ?>" />
			</label>
		</p>
		<p style="width:48%;float:left;">
			<label for="<?php echo $this->get_field_id('maximum_age'); ?>"><?php _e('Maximum Age:', 'mini-loops' );?>
				<input class="widefat" id="<?php echo $this->get_field_id('maximum_age'); ?>" name="<?php echo $this->get_field_name('maximum_age'); ?>" type="number" value="<?php echo $maximum_age; ?>" />
				<small>(<?php _e('Only posts less than X days old.', 'mini-loops');?>) </small>
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo $this->get_field_id('post_author'); ?>"><?php _e('Author:', 'mini-loops');?>
				<input class="widefat" id="<?php echo $this->get_field_id('post_author'); ?>" name="<?php echo $this->get_field_name('post_author'); ?>" type="text" value="<?php echo $post_author; ?>" /><br />
				<small>(<?php _e('IDs, comma-separated.', 'mini-loops');?>) </small>
			</label>
		</p>
		<p>
		<p style="width:48%;float:left;">
			<label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post Type:', 'mini-loops' );?>
				<select class="widefat" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
				<?php
					echo "<option value='any'" . selected( 'any', $post_type, false ) . ">Any</option>";
					$pts = get_post_types( array( 'public' => true ), 'objects' );
					foreach($pts as $slug=>$obj) {
						echo "<option value='{$slug}'" . selected( $slug, $post_type, false ) . ">{$obj->labels->name}</option>";
					}
				?>
				</select>
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo $this->get_field_id('post_status'); ?>"><?php _e('Post Status:', 'mini-loops' );?>
				<select class="widefat" id="<?php echo $this->get_field_id('post_status'); ?>" name="<?php echo $this->get_field_name('post_status'); ?>">
				<?php
					echo "<option value='any'" . selected( 'any', $post_status, false ) . ">Any</option>";
					$pss = get_available_post_statuses();
					foreach($pss as $k=>$v) {
						echo "<option value='{$v}'" . selected( $v, $post_status, false ) . ">{$v}</option>";
					}
				?>
				</select>
			</label>
		</p>
		<p style="width:48%;float:left;">
			<label for="<?php echo $this->get_field_id('order_by'); ?>"><?php _e('Order by:', 'mini-loops' );?>
				<select class="widefat" id="<?php echo $this->get_field_id('order_by'); ?>" name="<?php echo $this->get_field_name('order_by'); ?>">
				<?php
					$obs = array( 'id' => 'ID', 'author' => __('Author', 'mini-loops'), 'title' => __('Title', 'mini-loops'), 'date' => __('Date', 'mini-loops'), 'modified' => __('Last-modified Date', 'mini-loops'), 'parent', __('Parent ID', 'mini-loops'), 'rand' => __('Random', 'mini-loops'), 'comment_count' => __('Comment Count', 'mini-loops'), 'menu_order' => __('Menu Order', 'mini-loops'), 'meta_value' => __('Meta Value*', 'mini-loops'), 'meta_value_num' => __('Meta Value Numerical*', 'mini-loops') );
					foreach($obs as $k=>$v) {
						echo "<option value='{$k}'" . selected( $k, $order_by, false ) . ">{$v}</option>";
					}
				?>
				</select>
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order:', 'mini-loops' );?>
				<select class="widefat" id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
				<?php
					$obs = array( 'ASC', 'DESC' );
					foreach($obs as $v) {
						echo "<option value='{$v}'" . selected( $v, $order, false ) . ">{$v}</option>";
					}
				?>
				</select>
				<small>(<?php _e('ABC vs ZYX', 'mini-loops');?>)</small>
			</label>
		</p>
		<p style="clear:both;">
			<label for="<?php echo $this->get_field_id('order_meta_key'); ?>"><?php _e('*Meta key required for meta value ordering', 'mini-loops');?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('order_meta_key'); ?>" name="<?php echo $this->get_field_name('order_meta_key'); ?>" value="<?php echo $order_meta_key; ?>" />
		</p>
		<p style="clear:both;">
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('reverse_order'); ?>" name="<?php echo $this->get_field_name('reverse_order'); ?>"<?php checked( $reverse_order ); ?> />
			<label for="<?php echo $this->get_field_id('reverse_order'); ?>"><?php _e('Show posts in reverse order?', 'mini-loops');?></label>
			<small>(<?php _e('ABC vs CBA', 'mini-loops');?>)</small>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('shuffle_order'); ?>" name="<?php echo $this->get_field_name('shuffle_order'); ?>"<?php checked( $shuffle_order ); ?> />
			<label for="<?php echo $this->get_field_id('shuffle_order'); ?>"><?php _e('Shuffle post order?', 'mini-loops');?></label>
			<small>(<?php _e('ABC vs BCA', 'mini-loops');?>)</small>
		</p>
		<p style="width:48%;float:left;">
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('ignore_sticky'); ?>" name="<?php echo $this->get_field_name('ignore_sticky'); ?>"<?php checked( $ignore_sticky ); ?> />
			<label for="<?php echo $this->get_field_id('ignore_sticky'); ?>"><?php _e('Unstick sticky posts?', 'mini-loops');?></label>
		</p>
		<p style="width:48%;float:right;">
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('only_sticky'); ?>" name="<?php echo $this->get_field_name('only_sticky'); ?>"<?php checked( $only_sticky ); ?> />
			<label for="<?php echo $this->get_field_id('only_sticky'); ?>"><?php _e('Only sticky posts?', 'mini-loops');?></label>
		</p>
		<p style="width:48%;float:left;">
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('exclude_sticky'); ?>" name="<?php echo $this->get_field_name('exclude_sticky'); ?>"<?php checked( $exclude_sticky ); ?> />
			<label for="<?php echo $this->get_field_id('exclude_sticky'); ?>"><?php _e('Exclude sticky posts?', 'mini-loops');?></label>
		</p>
		<p style="clear:both;"><small><?php _e( '"Only" will take precedence if both sticky options are checked.', 'mini-loops' ); ?></small></p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('exclude_current'); ?>" name="<?php echo $this->get_field_name('exclude_current'); ?>"<?php checked( $exclude_current ); ?> />
			<label for="<?php echo $this->get_field_id('exclude_current'); ?>"><?php _e('If viewing a single post, exclude it?', 'mini-loops');?></label>
		</p>
		<p style="clear:both;"><small><?php _e( 'This option is ignored if "Only sticky" is checked.', 'mini-loops' ); ?></small></p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('current_category'); ?>" name="<?php echo $this->get_field_name('current_category'); ?>"<?php checked( $current_category ); ?> />
			<label for="<?php echo $this->get_field_id('current_category'); ?>"><?php _e('Get posts from current category (if archive)?', 'mini-loops');?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('current_single_category'); ?>" name="<?php echo $this->get_field_name('current_single_category'); ?>"<?php checked( $current_single_category ); ?> />
			<label for="<?php echo $this->get_field_id('current_single_category'); ?>"><?php _e('Get posts from first category (if single)?', 'mini-loops');?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('current_author'); ?>" name="<?php echo $this->get_field_name('current_author'); ?>"<?php checked( $current_author ); ?> />
			<label for="<?php echo $this->get_field_id('current_author'); ?>"><?php _e('Get posts from current author (if single or archive)?', 'mini-loops');?></label>
		</p>

	</div>

	<div style="width:48%;float:right;">

		<p style="width:48%;float:left;">
			<label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories:', 'mini-loops');?>
				<input class="widefat" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" type="text" value="<?php echo $categories; ?>" /><br />
				<small>(<?php _e('IDs, comma-separated.', 'mini-loops');?>) </small>
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e('Tags:', 'mini-loops');?>
				<input class="widefat" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>" type="text" value="<?php echo $tags; ?>" /><br />
				<small>(<?php _e('IDs, comma-separated.', 'mini-loops');?>) </small>
			</label>
		</p>
			<label for="<?php echo $this->get_field_id('tax'); ?>"><?php _e('Custom Taxonomies:', 'mini-loops');?>
				<input class="widefat" id="<?php echo $this->get_field_id('tax'); ?>" name="<?php echo $this->get_field_name('tax'); ?>" type="text" value="<?php echo $tax; ?>" /><br />
				<small>(<?php _e('Ex: category=1,2,4&amp;post_tag=6,12', 'mini-loops');?><br />
						<?php _e('Available: ', 'mini-loops'); echo implode( ', ', get_taxonomies( array( 'public' => true ) ) ); ?>)</small>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('custom_fields'); ?>"><?php _e('Custom Fields:', 'mini-loops');?>
				<input class="widefat" id="<?php echo $this->get_field_id('custom_fields'); ?>" name="<?php echo $this->get_field_name('custom_fields'); ?>" type="text" value="<?php echo $custom_fields; ?>" /><br />
				<small>(<?php _e('Ex: meta_key=meta_value&amp;meta_key2=meta_value2', 'mini-loops');?>)</small>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Exclude Posts:', 'mini-loops');?>
				<input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" /><br />
				<small>(<?php _e('IDs, comma-separated.', 'mini-loops');?>)</small>
			</label>
		</p>
		<h3 style="clear:both;"><?php _e( 'Format', 'mini-loops' ); ?></h3>
		<p style="width:48%;float:left;">
			<label for="<?php echo $this->get_field_id('before_items'); ?>"><?php _e('Before Item:', 'mini-loops');?>
				<?php /*<input class="widefat" id="<?php echo $this->get_field_id('before_items'); ?>" name="<?php echo $this->get_field_name('before_items'); ?>" type="text" value="<?php echo htmlspecialchars( stripslashes( $before_items ) ); ?>" />*/?>
				<textarea class="widefat" id="<?php echo $this->get_field_id('before_items'); ?>" name="<?php echo $this->get_field_name('before_items'); ?>"><?php echo htmlspecialchars( stripslashes( $before_items ) ); ?></textarea>
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo $this->get_field_id('after_items'); ?>"><?php _e('After Item:', 'mini-loops');?>
				<?php /*<input class="widefat" id="<?php echo $this->get_field_id('after_items'); ?>" name="<?php echo $this->get_field_name('after_items'); ?>" type="text" value="<?php echo htmlspecialchars( stripslashes( $after_items ) ); ?>" />*/?>
				<textarea class="widefat" id="<?php echo $this->get_field_id('after_items'); ?>" name="<?php echo $this->get_field_name('after_items'); ?>"><?php echo htmlspecialchars( stripslashes( $after_items ) ); ?></textarea>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('item_format'); ?>"><?php _e('Item Format:', 'mini-loops');?>
				<textarea class="widefat" rows="5" id="<?php echo $this->get_field_id('item_format'); ?>" name="<?php echo $this->get_field_name('item_format'); ?>"><?php echo stripslashes( $item_format ); ?></textarea>
			</label>
			<small><em><a href="http://wordpress.org/extend/plugins/mini-loops/other_notes/"><?php _e('See an explanation of options.', 'mini-loops');?></a></em></small>
		</p>

	</div>

	<div style="clear:both;"></div>