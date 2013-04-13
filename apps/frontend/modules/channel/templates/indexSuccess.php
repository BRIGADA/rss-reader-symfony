<div class="hero-unit">
	<h1>Привет, мир!</h1>
	<p>Это список RSS-каналов, которые обслуживаются системой.</p>
	<p><a href="<?php echo url_for('channel/new') ?>" class="btn btn-primary btn-large">Добавить &raquo;</a></p>
</div>
<div class="row">
	<?php foreach ($channels as $channel) : ?>
	<div class="span6">
		<div class="content">
			<h2><a href="<?php echo url_for("show/{$channel->id}")?>"><?php echo $channel->title?></a></h2>
			<?php // echo image_tag($channel->logo_url)?>
			<p><?php echo $channel->description?></p>
			<hr/>
		</div>
	</div>
	<?php endforeach ?>
</div>
