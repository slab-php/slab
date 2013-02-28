# Slab

## A PHP5 framework.

Slab is/was an MVC framework inspired by CakePHP and CodeIgniter, but I actually want to move away from the strict MVC paradigm and loosen it up.


## Views

### Helpers

Helpers are included directly in the scope of each view. There are two helpers: `$html` and `$number`. They can be used in the view like so:

	<p>Link to <a href="<?php e($html->url('/pages/help')); ?>">HELP</a></p>


#### `$html`

##### url

`url($u)`: Wraps `dispatcher->url` which returns either a relative path to a static file or a path to a controller action, optionally using url rewriting for pretty, SEO friendly URLs if enabled (default), optionally including a session ID if the session ID is persisted via the URL.

For example, if the site is hosted at `www.domain.com/some/application/`, `url('/pages/home')` may  return (depending on the environment):

- `/some/application/pages/home`
- `/some/application/slab.php?url=/pages/home`
- `/some/application/pages/home?session_id=HASH`
- `/some/application/slab.php?url=/pages/home&session_id=HASH`

If there exists a file at `www.domain.com/some/application/images/header.jpg`, `url('/images/header.jpg')` would return `/some/application/images/header.jpg`.

`$html->url('...')` is therefore the recommended method for producing a relative URL to either a controller action or a static file.


##### markdown

`markdown($markdownText)`: passes the [Markdown](http://daringfireball.net/projects/markdown/) formatted input through [Markdown Extra](http://michelf.com/projects/php-markdown/) and returns the resultant HTML


##### label

`label($forId, $value)`: Returns a HTML `label` element. Eg.:

    <p><?php e($html->label('data[name]', 'Name:')); ?></p>

results in:

	<p><label for='data[name]'>Name:</label></p>


##### inputHidden

`inputHidden($params)`: returns a hidden input element. Params is an array containing optionally `name`, `id` and `value`. Eg:

    <form><?php e($html->inputHidden(array(
    	'name' => 'data[name]',
    	'id' => 'name_element',
    	'value' => 'Steve'
   	))); ?></form>

results in:

	<form><label type='hidden' name='data[name]' id='name_element' value='Steve' /></form>


##### input, inputText, inputUrl, inputFile

`input($params)` also `inputText` `inputUrl` and `inputFile`: returns an input with an optional label. Params is an array containing optionally `name`, `id`, `value`, `label`, `type`. If `label` is not included or is null, no label will be output. The `inputText`, `inputUrl` and `inputFile` methods include the `type` value. Eg:

    <form><?php e($html->input(array(
    	'name' => 'data[name]',
    	'id' => 'name_element',
    	'value' => 'Adam',
    	'label => 'Name:',
    	'type' => 'text'
    ))); ?></form>

results in:

	<form><label for='name_element'>Name:</label> <input type='text' name='data[name]' id='name_element' value='Adam' /></form>


##### textarea

`textarea($params)`: returns a `textarea`. Params is an array containing optionally `name`, `id`, `value`, `label`, `rows` (default to 8) and `cols` (default to 80).


##### select

`select($params)`: returns a `select` element. Params is an array containing optionally `name`, `id`, `options`, `current` and `label`. Eg:

	<?php 
		$user['current_location'] = 'AU';
		$locations = array(
			'AU' => 'Australia',
			'NZ' => 'New Zealand',
			'US' => 'United States',
			'GB' => 'Great Britain'
	); ?>
	<p><?php e($html->locations(array(
		'name' => 'data[location]',
		'id' => 'location',
		'options' => $locations,
		'current' => $user['current_location'],
		'label' => 'Select your location'
	))); ?></p>

results in (reformatted):

    <p>
    	<label for='location'>Select your location</label>
    	<select name='data[location]' id='location'>
    		<option value='AU' selected="selected">Australia<option>
    		<option value='NZ'>New Zealand</option>
    		<option value='US'>United States</option>
    		<option value='GB'>Great Britain</option>
    	</select>
    </p>


##### selectIntFromRange

`selectIntFromRange($name, $id, $from, $to, $current)`: eg.:

    <?php e($html->selectIntFromRange('age', 'age', 0, 100, 32)); ?>

results in (reformatted):

    <select name='age' id='age'>
    	<option>0</option>
    	...
    	<option selected="selected">32</option>
    	...
    </select>


##### headerStatus

`headerStatus($code, $reason = null)`: Sets the HTTP header status. If the reason is not provided it uses a lookup table for standard HTTP status codes. Eg: `$html->headerStatus(501);` may result in `header('HTTP/1.1 501 Not Implemented');`.


##### headerNoCache

`headerNoCache()`: Writes the headers required to trigger `no-cache` for Internet Explorer.


## License

Licensed under the Attribution-ShareAlike 3.0 Generic ([CC BY-SA 3.0][ccsa]) license.

### Third-party licenses

#### [PHP Markdown & Extra][markdown_extra]

Copyright (c) 2004-2009 Michel Fortin

All rights reserved.

#### [Original Markdown][original_markdown]

Copyright (c) 2004-2006 John Gruber

All rights reserved.

#### Other third-party licenses

May contain other third-party components under other licenses that I have missed, I will try to keep this up to date.


[ccsa]: http://creativecommons.org/licenses/by-sa/3.0/
[markdown_extra]: http://michelf.com/projects/php-markdown
[original_markdown]: http://daringfireball.net/projects/markdown


