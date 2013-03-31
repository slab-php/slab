# BArray

BArray is an attempt to wrap a PHP array with some set comprehension logic.

## Rationale

Lets face it. PHP sucks. We wouldn't be here if we could use Ruby, Python, C#, or even *local deity forbid* Visual Basic. BArray wraps a PHP `array()` in some set comprehension logic to try to make it suck a bit less and help us pay the bills a bit faster.

## Use

BArray is developed for PHP 5.2+ although it is only developed and tested on a PHP 5.4 installation. That said it doesn't support cool features like anonymous functions which would make things a whole lot easier, because realistically your ridiculously cheap shared web host is probably running a three year old version of PHP anyway.

To wrap an array in BArray goodness:

	$dumbArray = array(
		array('id' => 0, 'deleted' => false).
		array('id' => 1, 'deleted' => 'true')
	);
	$smartArray = new BArray($dumbArray);

To apply a `filter` (or `where`):

	$nonDeletedThings = $smartArray->filter("@@['deleted'] = false");

Yeah that's right. The predicate is a string. BArray just uses `eval` in the background to apply the set comprehension, with a simple templating convention used where `@@` gets replaced with the item, so this is roughly equivalent to this:

	// C#:
	var nonDeletedThings = things.Where(t => !t.Deleted)

You are responsible for making sure script kiddies don't try to pass in `exec('rm ~/*')` or whatever. The method is executed immediately (no lazy eval) and the result is returned, so the source array is always copied. Also note that PHP may complain if you try to chain the method calls, depending on the runtime version and several astrological considerations.

To cast that back to a dumb array:

	foreach ($nonDeletedThings->to_array() as $thing) {
		...
	}

This obviously isn't intended for running complex logic over thousands of items but if you're doing things like that you should be asking yourself why are you writing crappy code on a crappy system that was designed for content management and still sleep at night. If you want high perf, high volume PHP look at how Facebook manages their infrastructure instead.

## Methods

### `get_array()` / `to_array()`
Returns the underlying array.

### `where($predicate)` / `filter($predicate)`
Applies the filter predicate to the underlying array and returns the result.

	$myFoos = $foos->filter("@@['belongs_to'] = 'Ben'");

### `select($predicate)` / `map($predicate)`
Applies the map method to the underlying array and returns the result.

	$ducks = $foos->filter("new Duck(@@['name'], @@['age'])");

### `select_many($predicate)` / `bind($predicate)`
Applies the filter predicate and returns the flattened result.

### `order_by($predicate)`
Applies a string sort using the predicate.

	$people = new BArray(array(
		array('Name'=> 'Patrick', 'Sign' => 'Taurus'),
		array('Name' => 'Ben', 'Sign' => 'Cancer'),
	));
	$orderedBySign = $people->order_by("@@['Sign']);

### `order_by_desc($predicate)`
Reversed order string sort.

### `count($predicate)`
Returns the integer count of items that satisfy the predicate - count(filter($predicate)).

### `sum($predicate)`
Returns the sum of the result of mapping the predicate - sum(map($predicate)).

	$totalAge = $people->sum("@@['Age']");

### `min($predicate)`
Returns the minimum result of mapping the predicate - min(map($predicate)).

	$youngestAge = $people->min("@@['Age']");

### `max($predicate)`
Returns the maximum result of mapping the predicate - min(map($predicate)).

	$oldestAge = $people->max("@@['Age']);

### `average($predicate)` / `avg($predicate)`
Returns the average result of mapping the predicate - avg(map($predicate)).

	$averageAge = $people->average("@@['Age']");

### `any($predicate)`
Returns `true` or `false` whether any elements satisfy the filter - `count(filter($predicate)) !== 0`.

	$anyMinors = $people->any("@@['Age'] < 18");

### `all($predicate)`
Returns `true` or `false` whether all elements satisfy the filter - `count(filter($predicate)) == count()`.

	$allAreProgrammers = $people->all("@@['profession'] == 'programmer'");


## Contribute

If you want to contribute to this project, start by forking the repo. Create an issue if applicable, create a branch in your fork, and create a pull request when it's ready. Thanks!


## License

Licensed under the Attribution-ShareAlike 3.0 Generic ([CC BY-SA 3.0](http://creativecommons.org/licenses/by-sa/3.0/)) license.


