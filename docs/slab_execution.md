# Slab execution

The entry point is `/slab.php` or an executable PHP file in the site

1. The base paths are set	
2. `/lib/bootstrap.php` is included
	1. Some global files are included, then the Bootstrap class
3. `Dispatcher::dispatch()` is called without any arguments, which causes it to take the context from the environment. This returns an `ActionResult`.
	1. Extract the controller name, action name, and parameters from the requested path (`$_REQUEST['slab_url']`) (or the configured `default_route` if no requested path).
4. The `ActionResult`'s `render()` method is called

