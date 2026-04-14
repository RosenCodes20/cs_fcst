<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);

require_once ("system/_layout.php");

// $id = get_var($_GET['category']);
$_RDATE = get_var($_GET['date']);
$data = $_GET;

// �������� �� ��������� �� ������

if ($_RDATE) {

	$_RDAY = substr( $_RDATE, 0, 2 );
	$_RMONTH = substr( $_RDATE, 3, 2 );
	$_RYEAR = substr( $_RDATE, 6, 4 );

	if (!checkdate( $_RMONTH, $_RDAY, $_RYEAR )) {

		$_RDATE = false;

		$file = "404.html";
		include ("templates/_layout.html");
		exit;
		
	} else {

		$_RDATE = date("d/m/Y", mktime(0, 0, 0, $_RMONTH, $_RDAY, $_RYEAR));
	}
}

$page = Page::get(6);

// Navigaton

$limit = 20;
$n_page = (int)( $data['page'] > 0 ? $data['page'] : 1 );

// ������������ ��� ������� �������� � ������, ��� � ������ �����

$_REMOVE_FROM_LINK[] = 'page';

$view = 6;

if ($n_page > 1)
	$view = 5;

$total_products = false;

$navigation = false;

$events = Event::search( array (
	'date' => $_RDATE ? cDate($_RDATE) : false,
	'navigation' => array ('limit' => $limit, 'page' => $n_page)
), $navigation );

$total_products = $navigation->records;
$total_pages = $total_products / $limit;

if ($total_pages > (int)$total_pages)
	$total_pages = (int)$total_pages + 1;

$temp = $n_page + $view > $total_pages ? $n_page + $view - $total_pages : 0;
$begin = $n_page - $view - $temp > 0 ? $n_page - $view - $temp : 1;
$temp = $n_page - $view < 1 ? $view - $n_page + 1 : 0;
$end = $n_page + $view + $temp < $total_pages ? $n_page + $view + $temp : $total_pages;

$link = ($data['category'] ? '&category='.$data['category'] : '').($_RDATE ? '&date='.$_RDATE : '');
$navigation_file = HOST."/events-list.php";

// --

$file = $page ? "events-list.html" : "404.html";
include ("templates/_layout.html");

?>