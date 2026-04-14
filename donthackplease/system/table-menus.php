<?php

if (!$table->fields)
	foreach ($table->records[0] as $key => $value)
		$table->fields[$key] = ucfirst($key);
		
if (!$table->key)
	$table->key = 'id';
	
$table->types->enum = array (
	'active'
);

$table->types->input = array (
	'position'
);

// Определяне на полетата при начин на извеждане disabled

if (!$table->disabled) {

	$table->disabled->fields = array();

	reset($table->fields);
	$key = key($table->fields);

	if ($key == $table->key)
		$table->disabled->fields[] = $key;
		
	$table->disabled->count = count($table->fields) - count($table->disabled->fields) + 1;
}

// Работни променливи

$table->submit = false;

require ("templates/table-menus.html");

?>