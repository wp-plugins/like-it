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
	<h2><?php _e('Like-it stats') ?></h2>
	
	<?php if($total_likes > $likeit_per_page): ?>
		<div class="tablenav">
			<div class="tablenav-pages">
				<span class="displaying-num"><?php printf(__('Displaying %d-%d of %d'), ($page-1)*$likeit_per_page+1, $page*$likeit_per_page, $total_likes)?></span>
				<?php 
					echo paginate_links( array(
						'base' => add_query_arg( 'paged', '%#%' ),
						'format' => '',
						'prev_text' => __('&laquo;'),
						'next_text' => __('&raquo;'),
						'total' => $total_likes,
						'current' => $page
					));
				?>
			</div>
		</div>
	<?php endif; ?>

	<table class="widefat">
		<?php $thead = "
			<thead>
				<tr>
					<th>IP</th>
					<th>" . __('Geolocation') . "</th>
					<th>" . __('Time') . "</th>
					<th>" . __('Post') . "</th>
				</tr>
			</thead>
		"; ?>
		
		<?php echo $thead; ?>
		<tbody>
			<?php foreach($likes as $single): ?>
				<tr>
					<td><?php echo $single->ip ?></td>
					<td>
						<?php echo implode(', ', array($single->ip_info->City, $single->ip_info->RegionName, $single->ip_info->CountryName)) ?>
					</td>
					<td><?php echo $single->time ?></td>
					<td>
						<a href="<?php echo $single->post_url?>" title="<?php echo $single->post_title?>"><?php echo $single->post_title?></a>
						(<?php echo $single->post_liked_count ?>)
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
		<?php echo $thead ?>
	</table>
</div>

