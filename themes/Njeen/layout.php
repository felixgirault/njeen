<!DOCTYPE html>

<html>
	<head>
		<meta charset="utf-8" />

		<title><?php echo $Blog->title; ?></title>

		<link rel="stylesheet" type="text/css" href="<?php echo $Theme->url( 'css/njeen.css' ); ?>" />
	</head>

	<body>
		<header class="page-header">
			<div class="container">
				<p class="blog-title"><?php echo Html::aLink( $Blog->title, 'Blog home', $Router->home( )); ?></p>

				<nav class="blog-menu">
					<ul>
						<li><?php echo Html::aLink( 'Articles', 'All articles', $Router->articles( )); ?></li>
						<li><?php echo Html::aLink( 'About', 'About Njeen', $Router->pages( 'about' )); ?></li>
					</ul>
				</nav>
			</div>
		</header>

		<div class="page">
			<div class="container">
				<?php echo $page; ?>
			</div>
		</div>

		<footer class="page-footer">
			<div class="container">
				<p>Powered by <a href="https://github.com/felixgirault/njeen">Njeen</a>.</p>
			</div>
		</footer>
	</body>
</html>
