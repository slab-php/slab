# Slab

## A PHP5 framework.
Slab is/was an MVC framework inspired by CakePHP and CodeIgniter, but I actually want to move away from the strict MVC paradigm and loosen it up.


## Views

Views are PHP files that are included in the `View::__render` method. In the scope of the view a number of objects are available:

- `$this` is the `View` instance
- The `$html` and `$number` helpers
- `$pageTitle` is available, and can be set within the view, and used when rendering the layout (eg. `<title><?php eh($pageTitle); ?></title>`)
- `$dispatcher`, the request-wide `Dispatcher` instance which can be used for rendering partials/subviews: `<ul><?php e($dispatcher->dispatch("/item/view/{$id}", array('data' => $data))->render_to_string()); ?></ul>`
- Anything set in the controller using the `set` method is first class, so if the controller action method uses `$this->set('foos', $this->fooService->get_foos());` the view will be able to do something like `<?php foreach ($foos as $foo): ?>...<?php endforeach; ?>`

### Partials / subviews
A partial view is produced by a controller action that returns `$this->partial()`, which is a `PartialView`. Calling `$view->render_to_string` then returns a string which can be displayed in the containing view. So I might have an item view partial in `controllers/my_item_controller.php`:

    function item_view($id) {
    	$this->set('item', $this->itemService->get($id));
		return $this->partial();
    }

The partial view itself (`views/my_item/item_view`):

	<li><?php e($item['name']); ?></li>

A main view then uses the partial in a loop:

    <h1>Items:</h1>
    <ul>
    	<?php foreach ($items as $item): ?>
			<?php e($dispatcher->dispatch("/my_item/item_view/{$item['id']}")->render_to_string()); ?>
        <?php endforeach; ?>
    </ul>

#### Shortcut method for consuming partials
`Dispatcher` includes a `partial()` method which returns the string result of the partial view. It also ensures the partial view is used by calling `$controller->partial()`. This results in a simplified shortcut method for creating and using partials. So the partial view action in `controllers/my_item_controller.php` is:

    function item_view($id) {
        $this->set('item', $this->itemService->get($id));
    }

The partial is used in another view as follows:

    <h1>Items:</h1>
    <ul>
		<?php foreach ($items as $item): ?>
			<?php e($dispatcher->partial("/my_item/item_view/{$item['id']}")); ?>
		<?php endforeach; ?>
	</ul>


## Models

The `Model` class in Slab is a wrapper around a simple database access layer. Generally services receive an instance of the `Model` class which has been initialised with a connection to the database and the name of the underlying table. The database schema is also loaded and used to escape fields. The `Model` class then provides methods that hide some of the complexity of querying the underlying table and provide typed and parameterised `INSERT` and `UPDATE` methods. The database is never too far under the surface though: `$foos = $model->find_all_by_query("SELECT * FROM Foos WHERE name = 'Ben'")` goes straight to the database and returns an array of hash arrays: `$foos[0]['name'] == 'Ben'`.

Note that if you are trying to save UTF-8 text (eg copying and pasting from MS Word which includes so-called 'smart quotes') to a database table which is set up for UTF-8 collation the site needs include this in the `head`:

	<head>
		<meta charset='utf-8'>
		...
		
See [this StackOverflow question](http://stackoverflow.com/questions/4696499/meta-charset-utf-8-vs-meta-http-equiv-content-type) for some discussion around this issue.

### `get_last_error`
Returns the last error generated in the current database connection.

### `find` / `get` / `load`
`find($id = null, $fields = null)`: Find the first model using `$id` against the default primary field name. `$fields` is an optional parameter which is an array of fields to include in the resulting model. If the value for `$id` is not provided or is null, `$model->id` is used instead (**although this is not recommended and will be deprecated and removed (#16) in a future version**).

### `find_all` / `get_all` / `load_all`
`find_all($conditions = null, $fields = null, $order = null)`:

- `$conditions` is an optional string containing the `WHERE` clause, or an array of conditions that are `AND`ed together
- `$fields` is an optional array of the field names to return
- `$order` is an optional string containing the `ORDER BY` clause

Both of these examples are functionally equivalent except that passing an array of conditions causes the field values to be escaped first:

	$things = $model->find_all("user_id = ${id} AND deleted = 0");
	$things = $model->find_all(array(
		'user_id' => $id,
		'deleted' => false
	));


### `find_all_by_query` / `get_all_by_query` / `load_all_by_query` / `query`
`find_all_by_query($sql)`: directly executes the SQL on the database and returns an array of hash arrays.

### `scalar($field, $conditions = null)`
Returns the specified field of the first row of the results given the conditions or null if no rows  were returned.

	$personsModel->scalar('MAX(age)', 'deleted = 0')

is roughly:

	SELECT MAX(age) FROM persons WHERE deleted = 0


### `find_by` / `get_by` / `load_by`
`load_by($key, $val)`: Find the first model by the given key and value. If the `$key` and `$val` are arrays they are ANDed together: `load_by(array('key1', 'key2'), array(1, '2'))`. If no results are found returns `null`.

### `find_all_by` / `get_all_by` / `load_all_by`
`find_all_by($key, $val = null, $fields = null, $order = null)`: returns all models matching the given key and value. `$key` can also be an array of key/values, eg: `$model->find_all_by(array('name' => 'Ben', 'age' => '32))`.

### `find_first` / `get_first` / `load_first`
`find_first($conditions = null, $fields = null, $order = null)`: returns the first model matching the criteria. `$conditions` is either a string containing the `WHERE` clause or an array of conditions that are `AND`ed together. Eg:

    $foo = $fooModel->find_first('age < 16', '*', 'age DESC');
    // $foo is the first oldest foo under 16
    $foo = $fooModel->find_first(array('age' => 65), '*', 'surname');
    // $foo is the first foo aged 65 by surname

### `save`
`save($data)`: saves the data to the database (in the configured table). If `$data` includes the primary field, performs an `UPDATE` otherwise performs an `INSERT` and returns the ID (the value of `mysql_insert_id()`).

### `update_field`
`update_field($id, $fieldName, $fieldData)`: updates a single field to the model identified by `$id`. Eg:

    $pageModel->update_field(16, 'content', $newContent);

### `remove` / `del` / `delete`
`remove($id)`: deletes the row with the given ID.

### `remove_all` / `del_all` / `delete_all`
`remove_all($conditions = null)`: deletes all rows that meet the given conditions (either a  string `WHERE` clause or an array of key/values which are `AND`ed together) or all rows if no condition is provided.

### `exists`
`exists($conditions)`: returns whether one or more models exist in the database that satisfy the given conditions  - either a string `WHERE` clause or an `AND`ed key/value array.

### `count`
`count($conditions = null)`: returns the count of rows that satisfy the condition - either a string `WHERE` clause or an `AND`ed key/value array.


## Controllers

### Action names
The action is found from the request path (`/controller/action/parameters`) and is matched to a method in the relevant controller ("dispatched"). Action names that start with double-underscores (eg. `function __authenticate() {...}`). This is to allow 'private' methods that cannot be accessed by the Slab dispatch process. Action names can start with a single underscore which is used as a convention for partial views although this is not enforced and does not add any magic functionality - any action can result in a partial response and actions starting with an underscore do not have to be partials and are not necessarily returned as partials.

### before_action / before_filter
`before_action()` and `before_filter()` are called (in that order) _after_ the cookie and components are added and intialised just prior to dispatching the action method. This is a good spot to initialise any services used by the controller or to call `__authenticate()` in controllers that have security concerns.

### after_action / after_filter
`after_action()` and `after_filter()` are called (in that order) immediately after calling the action method and ensuring that the view (or `actionResult`) is set.

### The SlabInternals controller
`SlabInternalsController` is a special built-in controller that is used to support error handling. If you happen to create a controller path named `slab_internals` it won't be picked up by the dispatcher as it will short-circuit to the built-in controller.

### Methods available to controllers
#### url
Wraps `$dispatcher->url()`.

#### set
Sets values in the view's data:

	// in the controller:
    function action() {
    	$this->set('items', $this->itemService->getAll());
	}
	
	// in the view:
	<ul>
		<?php foreach ($items as $item): ?>
			<li><?php e($item['name']); ?></li>
		<?php endforeach; ?>
	</ul>

#### set_layout($layout)
Set the view's layout (calls `$this->view->set_layout()`).

#### set_view($view = null, $layout = null)
Sets the action result to view and optionally the layout. This is used as the default action result from an action.

#### partial($view = null, $contentType = 'text/html')
Sets the action result to a `PartialResult` which renders the view without a layout.

#### redirect($url)
Sets the action result to a `RedirectResult` which redirects the client to the specified URL using a `302 FOUND` HTTP response.

#### `redirect_refresh($url)`
Sets the action result to a `RedirectRefreshResult` which sets a `Refresh` HTTP header which causes a browser redirection to the specified URL.

#### `text($s)`
Sets the action result to a `TextResult` which just returns the given string.

#### `json($o)`
Sets the action result to a `JsonResult` which returns the given object as a serialised JSON string using `json_encode`.

#### `file_inline($filename, $data, $encoding = 'binary')`
Sets the action result to a `FileResult` which returns a file with the given filename and binary data type (by default) and an `inline` content disposition.

#### `file_attachment($filename, $data, $encoding = 'binary')`
Sets the action result to a `FileResult` which returns a file with the given file name and binary data type (by default) and an `attachment` content disposition. This causes the browser to open the file using the file save dialog ('What do you want to do with this file?' etc).

This example assumes that you have a database table with an ID, a `name` column containing a filename, and a `data` column (`BLOB` or `LONGBLOB`, etc) containing binary data:

	function get_file($id) {
		$files = $this->dispatcher->load_model('files');
    	$file = $files->get($id);
        $this->file_attachment($file['name'], $file['data']);
	}
	// in a view, <a href="<?php e($html->url('/controller/get_file/3'); ?>">...

#### `ajax($statusCode, $data = null)`
Sets the action result to an `AjaxResult` which sets the specified HTTP status code and includes the optional data in the body of the response.

#### `ajax_success($data = null)`
Sets the action result to an `AjaxResult` with a HTTP status code of `200 OK` and includes the optional data in the body of the response.

#### `ajax_failure($data = null) / ajax_error($data = null)`
Sets the action result to an `AjaxResult` with a HTTP status code of `500 Internal Server Error` and includes the optional data in the body of the response.  

#### `file_not_found`
Sets the action result to an `AjaxResult` with a HTTP status code of `404 Not Found`.

#### `action($cap, $data = null)`
Executes another action (via `$this->dispatcher->dispatch($cap, $data)`) and uses the result of that action for this action (nested dispatch).

#### `object_result($obj)`
Sets the action result to an `ObjectResult` with the provided object data. This is not generally used for normal actions. Rather it would be used by a shared action that is only used via nested dispatch where a PHP object needs to be returned.

#### `controller_result($controller)`
Sets the action result to a `ControllerResult` containing the given controller (usually `$this`). This allows passing a full controller back to an action via a nested dispatch to allow really funky actions.

#### `physical_file($filename)`
Sets the action result to a `FileResult` containing the data read from the specified file. This is used to allow returning a physical file which would otherwise not be available via a `redirect` action. For example, returning `/etc/passwd` (which would be a **really bad idea**).

#### `redirect_immediate`
Dirty way of redirecting by setting the location header then calling `die()` to terminate the script. Because this bypasses the Slab lifecycle this stops cookies from being saved etc.

## Helpers

Helpers are included directly in the scope of each view. There are two helpers: `$html` and `$number`. They can be used in the view like so:

	<p>Link to <a href="<?php e($html->url('/pages/help')); ?>">HELP</a></p>

### `HtmlHelper - $html`
Most of the html methods relate to creating inputs which can be a bit verbose. These are most handy with the `select` statements where the options could come from a database, so it would save a lot of boilerplate code.

The most used method is `url()`, which is the recommended method for producing a relative URL to either a controller action or a static file.

#### `url`
`url($u)`: Wraps `dispatcher->url` which returns either a relative path to a static file or a path to a controller action, optionally using url rewriting for pretty, SEO friendly URLs if enabled (default), optionally including a session ID if the session ID is persisted via the URL.

For example, if the site is hosted at `www.domain.com/some/application/`, `url('/pages/home')` may  return (depending on the environment):

- `/some/application/pages/home`
- `/some/application/slab.php?url=/pages/home`
- `/some/application/pages/home?session_id=HASH`
- `/some/application/slab.php?url=/pages/home&session_id=HASH`

If there exists a file at `www.domain.com/some/application/images/header.jpg`, `url('/images/header.jpg')` would return `/some/application/images/header.jpg`.

#### `markdown($markdownText)`
Passes the [Markdown](http://daringfireball.net/projects/markdown/) formatted input through [Markdown Extra](http://michelf.com/projects/php-markdown/) and returns the resultant HTML. This is really cool within a view:

	<?php e($html->markdown(<<<EOT
	### Markdown example
	[Markdown](http://daringfireball.net/projects/markdown) is supported directly inside a view using the `\$html` helper.
	EOT
	)); ?>
	
Note that `$` signs need to be escaped using `\$` within PHP's [heredoc syntax](http://www.php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc).
	

#### `label`
`label($forId, $value)`: Returns a HTML `label` element. Eg.:

    <p><?php e($html->label('data[name]', 'Name:')); ?></p>

results in:

	<p><label for='data[name]'>Name:</label></p>

#### `input_hidden`
`input_hidden($params)`: returns a hidden input element. Params is an array containing optionally `name`, `id` and `value`. Eg:

    <form><?php e($html->input_hidden(array(
    	'name' => 'data[name]',
    	'id' => 'name_element',
    	'value' => 'Steve'
   	))); ?></form>

results in:

	<form><label type='hidden' name='data[name]' id='name_element' value='Steve' /></form>

#### `input`, `input_text`, `input_url`, `input_file`
`input($params)` also `input_text` `input_url` and `input_file`: returns an input with an optional label. Params is an array containing optionally `name`, `id`, `value`, `label`, `type`. If `label` is not included or is null, no label will be output. The `input_text`, `input_url` and `input_file` methods include the `type` value. Eg:

    <form><?php e($html->input(array(
    	'name' => 'data[name]',
    	'id' => 'name_element',
    	'value' => 'Adam',
    	'label => 'Name:',
    	'type' => 'text'
    ))); ?></form>

results in:

	<form><label for='name_element'>Name:</label> <input type='text' name='data[name]' id='name_element' value='Adam' /></form>

#### `textarea`
`textarea($params)`: returns a `textarea`. Params is an array containing optionally `name`, `id`, `value`, `label`, `rows` (default to 8) and `cols` (default to 80).

#### `select`
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

#### `select_int_from_range`
`select_int_from_range($name, $id, $from, $to, $current)`: eg.:

    <?php e($html->select_int_from_range('age', 'age', 0, 100, 32)); ?>

results in (reformatted):

    <select name='age' id='age'>
    	<option>0</option>
    	...
    	<option selected="selected">32</option>
    	...
    </select>

#### `header_status`
`header_status($code, $reason = null)`: Sets the HTTP header status. If the reason is not provided it uses a lookup table for standard HTTP status codes. Eg: `$html->header_status(501);` may result in `header('HTTP/1.1 501 Not Implemented');`.

#### `header_no_cache`
`header_no_cache()`: Writes the headers required to trigger `no-cache` for Internet Explorer.


### NumberHelper - $number

#### currency
#### format

## Components
Components are used in controllers. There are several built-in controllers, all of which subclass `Component`. Each component has an `init()` method that is called before the component's `before_action()` and `before_filter()` methods. Then after the action method is dispatched and the controller's `after_action()` and `after_filter()` methods are called, the component's `after_action()` anf `after_filter()` methods are called, followed by the component's `shutdown()` method.

`before_action()` and `before_filter()` should be considered synonyms for convenience and probably shouldn't both be implemented in the same component. Likewise `after_action()` and `after_filter()` should be considered convenience synonyms.

Components are available in each controller's class-wide scope, so within a controller action method:

    function some_action() {
    	$this->file->write('/temp/foo.txt', 'some text');
    }

### Cookie
### Db

### Email
The email component makes sending a formatted email relatively simple. The originating view will contain a form:

    /app/views/contact/index.php:
    	...
    	<form action="<?php e($html->url('/contact/submit')); ?>">
    		<input type="text" name="contact_email"/>
    		<button type="submit">Send</button>
    	</form>

The `/contact/submit` action does the work of getting the content (by way of a partial view) and sending the email:

    /app/controllers/contact_controller.php:
    	...
    	function submit() {
    		$this->email->send(array(
    			'to' => 'helloworld@swxben.com',
    			'subject' => 'Contact form',
    			'content' => $this->dispatcher->partial('/contact/_submit_email', array(
    				'contactEmail' => $this->data['contact_email']
    			))
    		));
    	}

The partial view converts the input into a formatted email:

	/app/controllers/contact_controller.php:
		...
		function _submit_email() {
			$this->set($this->data);
		}
	/app/views/contact/_submit_email.php:
		...
		Email address: <?php eh($contactEmail); ?>
		...

The available settings that can be passed to `email->send` are:

- `to`: the recipient address (either `mail@somewhere.com` or `A Person <mail@somewhere.com>`)
- `from`: the sending address (either `mail@somewhere.com` or `A Person <mail@somewhere.com>`)
- `subject`
- `content`
- `attachments`: an associative array, where the key is a filename and the value is the binary data of the attachment

#### Email attachments
Attachments are added to emails like so:

	$this->email->send(array(
		// ...
		'attachments' => array(
			'filename.pdf' => 0xDATA,
			'filename2.pdf' => 0xDATA
		)
	));

So in a controller action, a posted file could be added to an email using the `file` helper:

	$this->email->send(array(
		// ...
		'attachments' => array(
			$this->data['posted_file']['name'] => $this->file->read_posted_file($this->data['posted_file'])
		)
	));

#### HTML formatted email
The content type of the email can be set using the `'content_type'` setting. By default the content type is `text/plain`. HTML formatted email can be sent by setting the content type to `text/html`:

	$this->email->send(array(
		'to' => 'helloworld@swxben.com',
		'content' => '<p><strong>HTML email</strong></p>',
		'content_type' => 'text/html'
	));


### File
#### `exists($filename)`
#### `is_posted_file`
Returns whether the POSTed value is a posted file. A view might post to an action:
 
    <form method="POST" action="<?php e($html->url('/files/upload')); ?>" enctype="multipart/form-data">
		<input type="file" name="file"/>
		<button type="submit">Upload</button>
	</form>

Then in the action:

	class FilesController extends AppController {
		function upload() {
			if ($this->file->is_uploaded_file($this->data['file'])) {
				$this->text('A file was uploaded');
			} else {
				$this->text('No file was uploaded');
			}
		}
	}

#### `load_posted_file` / `read_posted_file`
Reads a posted file to a byte buffer. A view might post to an action:

    <form method="POST" action="<?php e($html->url('/files/upload')); ?>" enctype="multipart/form-data">
		<input type="file" name="file"/>
		<button type="submit">Upload</button>
	</form>

Then in the action:

    class FilesController extends AppController {
    	function upload() {
    		$filename = $this->data['file']['name'];
			$fileData = $this->file->read_posted_file($this->data['file']);
			$this->filesTable->save(array(
				'filename' => $filename,
				'data' => $fileData
			));
    	}
    }

#### `read` / `read_text`
#### `write` / `write_text`
#### `write_object($filename, $data, $mode = 'wb', $useEncryption = true)`
#### `read_object($filename, $useEncryption = true)`

#### `remove($filename)` / `delete($filename)`
Wrappers for `unlink()`.

#### `dir($path, $filesOnly = false)`
Get a directory listing of the path. This includes any subdirectories and returns the full path to each entry.

#### `store_file($sourcePath, $destinationKey)`
Copies the specified file (`$postedFile['tmp_name']` is typical) to `/app/stored_files/{hashed $destinationKey}`. Files can then be read using `read_stored_file($destinationKey)`, so just the key has to be stored in the database.

#### `read_stored_file($destinationKey)`
Returns the contents of a stored file (located at `/app/stored_files/{hashed $destinationKey}`). Use this in a controller like so:

	function get_file($id) {
		$id = (int)$id;
		$data = $this->someModel->get($id);
		$storedFileData = $this->file->read_stored_file($data['destination_key']);
		$this->file_attachment($data['filename'], $storedFileData);
	}

#### `rename($source, $dest)`
Wraps `rename()` which moves the file `$source` to `$dest`.

#### `copy($source, $dest)`
Wraps `copy()` which copies the file `$source` to `$dest`.


### Image
#### `load_image_from_file($filename)`
#### `get_posted_image($postImg)`
#### `resize_image($source, $maxWidth, $maxHeight)`
#### `resize_image_horizontal($source, $newWidth)`
#### `resize_with_background($source, $width, $height, $hexRGB)`
#### `save_image_to_in_memory_jpeg($img)`

### Session


## Dispatcher
The dispatcher is part of the lifecycle of a page request. The dispatcher is available:

- in views in the local scope, as `$dispatcher`
- in controllers in the class scope, as `$this->dispatcher`

### Slugs
Slugs are a way of identifying a page throughout the request. The slug can be set in a view or controller action:

- in a view: `<?php $dispatcher->set_slug('contact'); ?>`
- in the action: `function contact() { $this->dispatcher->set_slug('contact'); }`

Then the slug can be used to control the layout via the `slug_is` function which returns a boolean:

	<ul class="main-menu">
		<li class="<?php e($dispatcher->slug_is('home') ? 'current' : ''); ?>">Home</li>
		<li class="<?php e($dispatcher->slug_is('contact') ? 'current' : ''); ?>">Contact</li>
	</ul>

The `slug_is` function can optionally take an array where if the slug is in the array it will return true:

	$dispatcher->slug_is(array('home, contact'))

The slug can also be retrieved by calling `$dispatcher->get_slug()`.

## Global functions
Global functions are defined in `global_functions.php` and are mostly shortcuts for echo and escape operations. Some of the functions are based on similar functions in CakePHP and CodeIgniter.

### `any($val)`
Returns true if the value is not null and has a count of more than 0. Kind of the reverse of `empty()` except that `!empty(1)` is true whereas `any(1)` is false.

### `e($s)`
### `eh($)`
### `env($key)`
### `get_mime_type($ext)`
### `get_mime_type_from_filename($filename)`
### `get_microtime()`
### `h($s)` / `html($s)`
### `hex2rgb($hex)`

### `length($a)`
Alias for `count($a)`

### `lowercase($s)`, `toLower($s)`, `lc($s)`, `low($s)`
### `pr($s)`
### `str_contains($s)`
### `str_starts_with($s)`
### `uppercase($s)`, `toUpper($s)`, `uc($s)`, `up($s)`
### `uuid_secure()`


## Plugins
Plugins are sets of controllers, views, etc., that are bundled together under `/app/plugins`. There are some built-in plugins that are part of Slab - the main one being the `SlabInterals` controller which is used for 'pretty' error screens.

The dispatcher resolves a controller in the following order:

1. Within the application's controllers
1. Within the application's plugins
1. Within the built-in Slab plugins

So the dispatcher would attempt a request to `/foo/bar/1` via:

1. `app/controllers/foo_controller.php` -> `FooController::bar(1)`
1. `app/plugins/foo/controllers/foo_controller.php` -> `FooController::bar(1)`
1. `lib/plugins/foo/controllers/foo_controller.php` -> `FooController::bar(1)`

See the Bootstrap example application for an example plugin that sets up a [Twitter Bootstrap Carousel component](http://twitter.github.com/bootstrap/javascript.html#carousel).

### Official plugins

- [slab-plugin-lightbox2](https://github.com/swxben/slab-plugin-lightbox2) - based on [Lightbox 2](http://lokeshdhakar.com/projects/lightbox2/)


## PageLogger
`PageLogger` is a very simple way of logging events throughout a page request. A page logger instance is created in the application's `slab.php` script, the `'slab_init'` event is logged, and the page logger is passed to the dispatcher:

	$pageLogger->log('slab_init');

This records the event at the current time (`get_microtime()`, in `global_functions.php`). The page logger is then passed to the dispatcher where it can be accessed at `$dispatcher->pageLogger`. Events can be logged at any point. Calling `$pageLogger->to_table()` returns the log as a HTML table. This could happen at the end of the site template if debugging is enabled (not currently part of Slab itself).


## Examples

### Bootstrap Breadcrumbs
[Twitter Bootstrap](http://twitter.github.com/bootstrap/) includes a [breadcumb component](http://twitter.github.com/bootstrap/components.html#breadcrumbs). Slab partials can be used to create reusable breadcrumbs. Start with a `_breadcrumbs` view in a `SharedController` - `controllers/shared_controller.php`:

	class SharedController extends AppController {
		function _breadcrumbs() {
			$this->set('crumbs', $this->data['crumbs']);
		}
	}

This action receives crumbs via the action data which will be shown later. The `views/shared/_breadcrumbs.php` view generates the markup for the breadcrumbs:

	<div class="row-fluid">
		<ul class="breadcrumb">
			<?php foreach ($crumbs as $description => $url): ?>
				<li <?php if (empty($url)): ?>class="current"<?php endif; ?> >
					<?php if (empty($url)): ?>
						<?php eh($description); ?>
					<?php else: ?>
						<a href="<?php e($url); ?>"><?php eh($description); ?></a>
						<span class="divider">/</span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

This loops through `$crumbs` which is an associative array where the key is the description for the crumb and the value is the url. If the url is null the crumb is treated as the top.

Views include the breadcrumbs at the top. This would be a widget view (`/widgets/view/15` for example):

	<?php e($dispatcher->partial('/shared/_breadcrumbs', array(
		'Home' => $html->url('/'),
		'Widgets' => $html->url('/widgets'),
		"Viewing widget <em>{$widget['name']}</em>" => null
	))); ?>

This would result in something like:

[Home](#) / [Widgets](#) / Viewing widget _sprocket_


## Contribute

If you want to contribute to this project, start by forking the repo. Create an issue if applicable, create a branch in your fork, and create a pull request when it's ready. Thanks!

## License

Licensed under the Attribution-ShareAlike 3.0 Generic ([CC BY-SA 3.0](http://creativecommons.org/licenses/by-sa/3.0/)) license.

Slab includes [BArray](https://github.com/slab-php/barray), an array wrapper which is maintained outside of the Slab project but by the [slab-php](https://github.com/slab-php) organisation.

### Third-party licenses

#### [PHP Markdown & Extra](http://michelf.com/projects/php-markdown)
Copyright (c) 2004-2009 Michel Fortin  
All rights reserved.

#### [Original Markdown](http://daringfireball.net/projects/markdown)
Copyright (c) 2004-2006 John Gruber  
All rights reserved.

#### [Twitter Bootstrap](http://twitter.github.com/bootstrap/)
Copyright 2012 Twitter, Inc  
Licensed under the Apache License v2.0  
<http://www.apache.org/licenses/LICENSE-2.0>

#### [CakePHP](http://cakephp.org/)
Parts of Slab are based on, inspired by, or originally copied from CakePHP.  
Copyright (c) Cake Software Foundation, Inc. (<http://cakefoundation.org>)  
MIT License (<http://www.opensource.org/licenses/mit-license.php>)  

#### [CodeIgniter](http://ellislab.com/codeigniter)
Parts of Slab are based on, inspired by, or originally copied from CodeIgniter.  
Copyright (c) 2008 - 2011, EllisLab, Inc.  
<http://ellislab.com/codeigniter/user-guide/license.html>

#### [Lightbox 2](http://lokeshdhakar.com/projects/lightbox2/)
A modified copy of Lightbox 2 is included as a plug-in. It is intended that it will be [pulled out into a standalone plugin](https://github.com/swxben/slab/issues/26).  
Licensed under the Creative Commons Attribution 2.5 License - <http://creativecommons.org/licenses/by/2.5/>



#### Other third-party licenses

May contain other third-party components under other licenses that I have missed, I will try to keep this up to date.



