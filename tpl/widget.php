<ul class="likeit-widget-posts">
<?php foreach($posts as $lpost): ?>

<?php $post_data = get_post($lpost->post_id, OBJECT); ?>

	<li>
		<h4>
			<a href="<?php echo get_permalink($lpost->post_id) ?>" title="<?php echo $post_data->post_title ?>">
			<?php echo $post_data->post_title ?> (<?php echo $lpost->likes ?>)
			</a>
		</h4>
	</li>

<?php endforeach; ?>
</ul>