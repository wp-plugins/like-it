<?php

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

?>

<div class="wrap">

<?php if(isset($updated)): ?>
	<div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
<?php endif ?>

<h2><?php _e('Like-it configuration') ?></h2>

<form action="" method="post">
	<table class="form-table">
		<tr>
			<th scope="row"><label for="likeit-text"><?php _e('Like-it button text') ?></label></th>
			<td><input type="text" id="likeit-text" name="likeit-text" value="<?php echo stripslashes(get_option('likeit-text'))?>" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="likeit-autodisplay"><?php _e('Display button') ?></label></th>
			<td>
				<input type="checkbox" id="likeit-autodisplay" name="likeit-autodisplay" <?php if(get_option('likeit-autodisplay') == 'on'): ?>checked="checked"<?php endif ?> />
				<label for="likeit-autodisplay">Append button to the end of each post</label>
				<br />
				<span class="description"><?php _e('If you want to place it elsewhere, you may use <code>&lt?php if(function_exists("likeit_button")) likeit_button(); ?&gt;</code> in your theme, but remember that it uses <code>get_the_ID()</code> to get current post\'s id') ?></span>
			</td>
		</tr>
		<tr>
			<th><input type="submit" value="<?php _e('Update options &raquo;')?>" /></th>
		</tr>
	</table>
</form>

</div>
