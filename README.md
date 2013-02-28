# Slab

## A PHP5 framework.

Slab is/was an MVC framework inspired by CakePHP and CodeIgniter, but I actually want to move away from the strict MVC paradigm and loosen it up.


## Views

### Models

The `Model` class in Slab is a wrapper around a simple database access layer. Generally services receive an instance of the `Model` class which has been initialised with a connection to the database and the name of the underlying table. The database schema is also loaded and used to escape fields. The `Model` class then provides methods that hide some of the complexity of querying the underlying table and provide typed and parameterised `INSERT` and `UPDATE` methods. The database is never too far under the surface though: `$foos = $model->findAllByQuery("SELECT * FROM Foos WHERE name = 'Ben'")` goes straight to the database and returns an array of hash arrays: `$foos[0]['name'] == 'Ben'`.

#### getLastError
Returns the last error generated in the current database connection.

#### find / get / load
`find($id = null, $fields = null)`: Find the first model using `$id` against the default primary field name. `$fields` is an optional parameter which is an array of fields to include in the resulting model. If the value for `$id` is not provided or is null, `$model->id` is used instead (**although this is not recommended and will be deprecated and removed (#16) in a future version**).

#### findAll / getAll / loadAll
`findAll($conditions = null, $fields = null, $order = null)`:

- `$conditions` is an optional string containing the `WHERE` clause
- `$fields` is an optional array of the field names to return
- `$order` is an optional string containing the `ORDER BY` clause

#### findAllByQuery / getAllByQuery / loadAllByQuery / query
`findAllByQuery($sql)`: directly executes the SQL on the database and returns an array of hash arrays.

#### findBy / getBy / loadBy
`loadBy($key, $val)`: Find the first model by the given key and value. If the `$key` and `$val` are arrays they are ANDed together: `loadBy(array('key1', 'key2'), array(1, '2'))`. If no results are found returns `null`.

#### findAllBy / getAllBy / loadAllBy
`findAllBy($key, $val = null, $fields = null, $order = null)`: returns all models matching the given key and value. `$key` can also be an array of key/values, eg: `$model->findAllBy(array('name' => 'Ben', 'age' => '32))`.

#### save
`save($data)`: saves the data to the database (in the configured table). If `$data` includes the primary field, performs an `UPDATE` otherwise performs an `INSERT` and returns the ID (the value of `mysql_insert_id()`).

#### updateField
`updateField($fieldName, $fieldData)`: updates a single field to the model identified by `$this->id`. **This will be deprecated and an `id` field added to the method: `updateField($id, $fieldName, $fieldData)` (#16).

#### remove / del / delete
`remove($id)`: deletes the row with the given ID.

#### removeAll / delAll / deleteAll
`removeAll($conditions = null)`: deletes all rows that meet the given conditions (either a  string `WHERE` clause or an array of key/values which are `AND`ed together) or all rows if no condition is provided.

#### exists
`exists($id = null, $conditions = null)`: returns whether the model specified by the given ID exists or if one or more models exist in the database that satisfy the given conditions  - either a string `WHERE` clause or an `AND`ed key/value array.

#### count
`count($conditions = null)`: returns the count of rows that satisfy the condition - either a string `WHERE` clause or an `AND`ed key/value array.


### Controllers

#### beforeAction / beforeFilter
`beforeAction()` and `beforeFilter()` are called (in that order) _after_ the cookie and components are added and intialised just prior to dispatching the action method. This is a good spot to initialise any services used by the controller or to call `__authenticate()` in controllers that have security concerns.

#### afterAction / afterFilter
`afterAction()` and `afterFilter()` are called (in that order) immediately after calling the action method and ensuring that the view (or `actionResult`) is set.


### Helpers

Helpers are included directly in the scope of each view. There are two helpers: `$html` and `$number`. They can be used in the view like so:

	<p>Link to <a href="<?php e($html->url('/pages/help')); ?>">HELP</a></p>


#### `$html`

Most of the html methods relate to creating inputs which can be a bit verbose. These are most handy with the `select` statements where the options could come from a database, so it would save a lot of boilerplate code.

The most used method is `url()`, which is the recommended method for producing a relative URL to either a controller action or a static file.

##### url

`url($u)`: Wraps `dispatcher->url` which returns either a relative path to a static file or a path to a controller action, optionally using url rewriting for pretty, SEO friendly URLs if enabled (default), optionally including a session ID if the session ID is persisted via the URL.

For example, if the site is hosted at `www.domain.com/some/application/`, `url('/pages/home')` may  return (depending on the environment):

- `/some/application/pages/home`
- `/some/application/slab.php?url=/pages/home`
- `/some/application/pages/home?session_id=HASH`
- `/some/application/slab.php?url=/pages/home&session_id=HASH`

If there exists a file at `www.domain.com/some/application/images/header.jpg`, `url('/images/header.jpg')` would return `/some/application/images/header.jpg`.


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


### Components
Components are used in controllers. There are several built-in controllers, all of which subclass `Component`. Each component has an `init()` method that is called before the component's `beforeAction()` and `beforeFilter()` methods. Then after the action method is dispatched and the controller's `afterAction()` and `afterFilter()` methods are called, the component's `afterAction()` anf `afterFilter()` methods are called, followed by the component's `shutdown()` method.

`beforeAction()` and `beforeFilter()` should be considered synonyms for convenience and probably shouldn't both be implemented in the same component. Likewise `afterAction()` and `afterFilter()` should be considered convenience synonyms.

Components are available in each controller's class-wide scope, so within a controller action method:

    function some_action() {
    	$this->file->write('/temp/foo.txt', 'some text');
    }

#### Cookie
#### Db
#### Email
#### File
#### Image
#### Session

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


