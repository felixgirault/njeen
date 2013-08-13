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
				<p class="blog-title"><?php echo $Html->aLink( $Blog->title, 'Blog home', $Router->home( )); ?></p>

				<?php if ( $Menu->hasItems( )): ?>
					<nav class="blog-menu">
						<ul>
							<?php foreach ( $Menu as $Item ): ?>
								<li><?php echo $Html->aLink( $Item->text, $Item->title, $Item->url ); ?></li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>
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
