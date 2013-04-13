<!DOCTYPE html>
<html>
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
  </head>
  <body>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="brand" href="#">RSS Reader</a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li class="active"><?php echo link_to('Главная', '@homepage') ?></li>
						<li><a href="#about">Каналы</a></li>
						<li><a href="#contact">Вход</a></li>
					</ul>
				</div>
				<!--/.nav-collapse -->
			</div>
		</div>
	</div>
	<div class="container">
		<?php echo $sf_content ?>
	</div>
	<footer class="footer">
		<div class="container muted">BRIGADA &copy; 2013</div>
	</footer>

</body>
</html>
