<?php
/* Default view for the Slab unit tests
** BJS20091101
*/

?><!DOCTYPE html>
<html>
<head>
<title>Slab Unit Tests</title>
<style type="text/css">
@import '<?php e($html->url('/css/meyerweb_reset.css')); ?>';
@import '<?php e($html->url('/css/page.css')); ?>';
</style>
<body>

<div id="header">
  <h1>Slab Unit Tests</h1>
  <h2>A <a href="http://www.belfryimages.com.au" title="Belfry Images">Belfry Images</a> Project</h2>
</div>

<div id="content">
	<?php e($pageContent); ?>
</div>

<div id="footer">
  <p>
    <a rel="license" href="http://creativecommons.org/licenses/by/2.5/au/" title="Creative Commons License"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/2.5/au/88x31.png" /></a>
    <span xmlns:dc="http://purl.org/dc/elements/1.1/" href="http://purl.org/dc/dcmitype/InteractiveResource" property="dc:title" rel="dc:type">Belfry Images 
    Slab</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="http://labs.belfryimages.com.au/projects/slab/" property="cc:attributionName" rel="cc:attributionURL">Belfry Images</a> 
    is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/2.5/au/">Creative Commons Attribution 2.5 Australia License</a>.
  </p>
  <p>
    Permissions beyond the scope of this license may be available at 
    <a xmlns:cc="http://creativecommons.org/ns#" href="http://www.belfryimages.com.au" rel="cc:morePermissions">www.belfryimages.com.au</a>.
  </p>
</div>

</body>
</html>