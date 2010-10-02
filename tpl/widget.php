<ul class="likeit-widget-posts">
<?php foreach($posts as $post): ?>

<?php $post_data = get_post($post->post_id, OBJECT); ?>

	<li>
		<h4>
			<a href="<?php get_permalink($post->post_id) ?>" title="<?php echo $post_data->post_title ?>">
			<?php echo $post_data->post_title ?> (<?php echo $post->likes ?>)
			</a>
		</h4>
	</li>

<?php endforeach; ?>
</ul>