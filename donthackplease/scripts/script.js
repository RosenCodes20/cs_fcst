
function menus_clear_url()
{
	var url = document.getElementById('url');
	var type = document.getElementById('type');
	var item_id = document.getElementById('item_id');
	var block_page = document.getElementById('block_page');
	var block_url = document.getElementById('block_url');
	
	url.value = "";
	type.value = "general";
	item_id.value = "";
	
	block_page.style.display = "none";
	block_url.style.display = "block";
	
	url.focus();
}