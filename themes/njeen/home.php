<article class="entry">
	<header class="entry-header">
		<h1 class="entry-title">Welcome!</h1>
		<p class="entry-subtitle">Consider yourself at home</p>
	</header>

	<div class="entry-body">
		<p>You may want to take a look at...</p>

		<ul>
			<li>a page: <?php echo Html::link( 'Lorem ipsum', $Router->single( 'pages', 'lorem-ipsum' )); ?></li>
			<li>an article: <?php echo Html::link( 'Hello World!', $Router->single( 'articles', 'hello-world' )); ?></li>
		</ul>
	</div>
</article>
