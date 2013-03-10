<?php e($html->markdown(<<<EOT

### Markdown example
[Markdown](http://daringfireball.net/projects/markdown) is supported directly inside a view using the `\$html` helper. For example:

	<?php e(\$html->markdown(<<<EOT
	### Markdown example
	[Markdown](http://daringfireball.net/projects/markdown) is supported directly inside a view using the `\\\$html` helper.
	EOT
	)); ?>

Note that `\$` signs need to be escaped using `\\\$` within PHP's [heredoc syntax](http://www.php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc).
EOT
)); ?>
