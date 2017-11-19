<?php
require_once('config.php');
$db = new Connect;
$permalink = isset($_GET['permalink']) ? $_GET['permalink'] : '';
if($permalink){
	function GetPostsByPemrlink($permalink){
		$permalink = strtolower(trim($permalink));
		$permalink = preg_replace('/[^a-z0-9-]/', '-', $permalink);
		$permalink = preg_replace('/-+/', "-", $permalink);
		$permalink = rtrim($permalink, '-');
		$permalink = preg_replace('/\s+/', '-', $permalink);
		return $permalink;
	}
	
	$details = $db -> prepare("SELECT text FROM posts WHERE permalink = :permalink LIMIT 1");
	$details -> execute(array('permalink' => GetPostsByPemrlink($permalink)));
	$post = $details -> fetch(PDO::FETCH_ASSOC);
	echo $post['text'];
}
?>