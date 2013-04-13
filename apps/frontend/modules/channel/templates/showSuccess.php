<div class="hero-unit">
	<h1><?php echo $channel->title ?></h1>
	<p><?php echo $channel->description ?></p>
	<?php if($channel->editor) : ?>
	<p>Редактор: <?php echo $channel->editor ?></p>
	<?php endif ?>
	<p><a href="#" class="btn btn-primary btn-large">Подписаться</a></p>
</div>
<div class="row">
	<?php foreach ($channel->getItemsChunk() as $item) : ?>
	<div class="span12">
		<div class="well2">
			<div class="muted pull-right">
				<small><?php echo $item->pubdate?> </small>
			</div>
			<h3>
				<a href="<?php echo url_for($item->link)?>" target="_blank"><?php echo $item->title ?> </a>
			</h3>
			<div>
				<?php echo html_entity_decode($item->description) ?>
			</div>
			<hr>
		</div>
	</div>
	<?php endforeach ?>
</div>
<script type="text/javascript">
$(window).scroll(function(){
	console.log($('.well2')[0].offsetTop);
	if($(document).height() - $(window).height() <= $(window).scrollTop() + 250) {
		//alert('aaaaaaaaa');
	}
});
</script>