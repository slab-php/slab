$(function(){
	$('a[rel="external"]').click(function(){
		window.open(this.href);
		return false;
	});
});