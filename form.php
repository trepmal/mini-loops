		<p style="width:63%;float:left;">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' );?> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>
		<p style="width:33%;float:right;padding-top:20px;height:20px;">
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hide_title'); ?>" name="<?php echo $this->get_field_name('hide_title'); ?>"<?php checked( $hide_title ); ?> />
			<label for="<?php echo $this->get_field_id('hide_title'); ?>"><?php _e('Hide Title?', 'miniloops');?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title_url' ); ?>"><?php _e( 'Title URL:' );?> 
				<input class="widefat" id="<?php echo $this->get_field_id('title_url'); ?>" name="<?php echo $this->get_field_name('title_url'); ?>" type="text" value="<?php echo $title_url; ?>" />
			</label>
		</p>
		<h3>Query</h3>
		<p style="width:48%;float:left;">
			<label for="<?php echo $this->get_field_id('number_posts'); ?>"><?php _e('Number of Posts:');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('number_posts'); ?>" name="<?php echo $this->get_field_name('number_posts'); ?>" type="number" value="<?php echo $number_posts; ?>" />
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo $this->get_field_id('post_offset'); ?>"><?php _e('Posts Offset:');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('post_offset'); ?>" name="<?php echo $this->get_field_name('post_offset'); ?>" type="number" value="<?php echo $post_offset; ?>" />
			</label>
		</p>
		<p style="width:48%;float:left;">
			<label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Post Type:');?> 
				<select class="widefat" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
				<?php
					$pts = get_post_types( array( 'public' => true ), 'objects' );
					foreach($pts as $slug=>$obj) {
						echo '<option value="' . $slug . '"' . selected($slug,$post_type,true) . '>' . $obj->labels->name . '</option>';
					}
				?>
				</select>
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo $this->get_field_id('post_status'); ?>"><?php _e('Post Status:');?> 
				<select class="widefat" id="<?php echo $this->get_field_id('post_status'); ?>" name="<?php echo $this->get_field_name('post_status'); ?>">
				<?php
					$pss = get_available_post_statuses();
					foreach($pss as $k=>$v) {
						echo '<option value="' . $k . '"' . selected($k,$post_status,true) . '>' . $v . '</option>';
					}
				?>
				</select>
			</label>
		</p>
		<p style="width:48%;float:left;">
			<label for="<?php echo $this->get_field_id('order_by'); ?>"><?php _e('Order by:');?> 
				<select class="widefat" id="<?php echo $this->get_field_id('order_by'); ?>" name="<?php echo $this->get_field_name('order_by'); ?>">
				<?php
					$obs = array( 'id' => 'ID', 'author' => 'Author', 'title' => 'Title', 'date' => 'Date', 'modified' => 'Last-modified Date', 'parent', 'Parent ID', 'rand' => 'Random', 'comment_count' => 'Comment Count', 'menu_order' => 'Menu Order' );
					foreach($obs as $k=>$v) {
						echo '<option value="' . $k . '"' . selected($k,$order_by,true) . '>' . $v . '</option>';
					}
				?>
				</select>
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order:');?> 
				<select class="widefat" id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
				<?php
					$obs = array( 'ASC', 'DESC' );
					foreach($obs as $v) {
						echo '<option value="' . $v . '"' . selected($v,$order,true) . '>' . $v . '</option>';
					}
				?>
				</select>
				<small>(<?php _e('ABC vs ZYX', 'miniloops');?>)</small>
			</label>
		</p>
		<p style="clear:both;">
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('reverse_order'); ?>" name="<?php echo $this->get_field_name('reverse_order'); ?>"<?php checked( $reverse_order ); ?> />
			<label for="<?php echo $this->get_field_id('reverse_order'); ?>"><?php _e('Show posts in reverse order?', 'miniloops');?></label>
			<small>(<?php _e('ABC vs CBA', 'miniloops');?>)</small>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('ignore_sticky'); ?>" name="<?php echo $this->get_field_name('ignore_sticky'); ?>"<?php checked( $ignore_sticky ); ?> />
			<label for="<?php echo $this->get_field_id('ignore_sticky'); ?>"><?php _e('Ignore sticky posts?', 'miniloops');?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('exclude_current'); ?>" name="<?php echo $this->get_field_name('exclude_current'); ?>"<?php checked( $exclude_current ); ?> />
			<label for="<?php echo $this->get_field_id('exclude_current'); ?>"><?php _e('If viewing a single post, exclude it?', 'miniloops');?></label>
		</p>
		<p style="width:48%;float:left;">
			<label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories:', 'miniloops');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" type="text" value="<?php echo $categories; ?>" /><br />
				<small>(<?php _e('IDs, comma-separated.', 'miniloops');?>) </small>
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e('Tags:', 'miniloops');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>" type="text" value="<?php echo $tags; ?>" /><br />
				<small>(<?php _e('IDs, comma-separated.', 'miniloops');?>) </small>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('tax'); ?>"><?php _e('Custom Taxonomies:', 'miniloops');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('tax'); ?>" name="<?php echo $this->get_field_name('tax'); ?>" type="text" value="<?php echo $tax; ?>" /><br />
				<small>(<?php _e('Ex: category=1,2,4&post_tag=6,12');?><br />
						Available: <?php print_r( implode(', ',get_taxonomies(array('public'=>true)) )); ?>)</small>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('custom_fields'); ?>"><?php _e('Custom Fields:', 'miniloops');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('custom_fields'); ?>" name="<?php echo $this->get_field_name('custom_fields'); ?>" type="text" value="<?php echo $custom_fields; ?>" /><br />
				<small>(<?php _e('Ex: meta_key=meta_value');?>)</small>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Exclude Posts:', 'miniloops');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" /><br />
				<small>(<?php _e('IDs, comma-separated.', 'miniloops');?>)</small>
			</label>
		</p>
		<h3 style="clear:both;">Format</h3>
		<p style="width:48%;float:left;">
			<label for="<?php echo $this->get_field_id('before_items'); ?>"><?php _e('Before Item:', 'miniloops');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('before_items'); ?>" name="<?php echo $this->get_field_name('before_items'); ?>" type="text" value="<?php echo htmlspecialchars( stripslashes( $before_items ) ); ?>" />
			</label>
		</p>
		<p style="width:48%;float:right;">
			<label for="<?php echo $this->get_field_id('after_items'); ?>"><?php _e('After Item:', 'miniloops');?> 
				<input class="widefat" id="<?php echo $this->get_field_id('after_items'); ?>" name="<?php echo $this->get_field_name('after_items'); ?>" type="text" value="<?php echo htmlspecialchars( stripslashes( $after_items ) ); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('item_format'); ?>"><?php _e('Item Format:', 'miniloops');?> 
				<textarea class="widefat" rows="5" id="<?php echo $this->get_field_id('item_format'); ?>" name="<?php echo $this->get_field_name('item_format'); ?>"><?php echo stripslashes( $item_format ); ?></textarea>
			</label>
		</p>
