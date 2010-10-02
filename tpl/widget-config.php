<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title') ?>:</label>
	<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
</p>
	
<p>
	<label for="<?php echo $this->get_field_id( 'displayed' ); ?>"><?php _e('Number of displayed posts') ?>:</label>
	<input type="text" id="<?php echo $this->get_field_id( 'displayed' ); ?>" name="<?php echo $this->get_field_name( 'displayed' ); ?>" value="<?php echo $instance['displayed']; ?>" class="widefat" />
</p>