<?php
require_once('config.php');
$db = new Connect;
?>
<!doctype html>
<html>
	<head>
		<title>
			Permalink tutorial
		</title>
		<link rel="stylesheet" href="css/style.css" />
	</head>
	<body>
		<?php 
		$action = isset($_GET['action']) ? $_GET['action'] : '';
		if($action == 'add'){
			
			// here we have our FilterPermalink function
			function FilterPermalink($link){
				$db = new Connect;
				$link = strtolower(trim($link));
				$link = preg_replace('/[^a-z0-9-]/', '-', $link);
				$link = preg_replace('/-+/', "-", $link);
				$link = rtrim($link, '-');
				$link = preg_replace('/\s+/', '-', $link);
				if(strlen($link) > 30)
					$link = substr($link, 0, 25);
				$existing_lnk = $db->prepare("SELECT id FROM posts WHERE permalink = :permalink");
				$existing_lnk->execute(array('permalink' => $link));
				$num = $existing_lnk->fetchAll(PDO::FETCH_COLUMN);
				$first_total = count($num);
				for($i=0;$first_total != 0;$i++){
					if($i == 0){
						$new_number = $first_total + 1;
						$newlink = $link."-".$new_number;
					}
					$check_lnk = $db->prepare("SELECT id FROM posts WHERE permalink = :permalink");
					$check_lnk->execute(array('permalink' => $newlink));
					$other = $check_lnk->fetchAll(PDO::FETCH_COLUMN);
					$other_total = count($other);
					if($other_total != 0){
						$first_total = $first_total + $other_total;
						$new_number = $first_total;
						$newlink = $link."-".$new_number;
					}elseif($other_total == 0){
						$first_total = 0;
					}           
				}
				if($i > 0)
					return $newlink;
				else
					return $link;
			}
			
			$title = isset($_POST['title']) ? $_POST['title'] : '';
			$title = stripslashes(htmlspecialchars($title));
			
			$permalink = isset($_POST['permalink']) ? $_POST['permalink'] : '';
			$permalink = FilterPermalink($permalink);
			
			$text = isset($_POST['text']) ? $_POST['text'] : '';
			$text = stripslashes(htmlspecialchars($text));
			
			$insert = $db->prepare("INSERT INTO posts SET title=:title, permalink=:permalink,text=:text");
			$insert -> execute(array(
				'title' 	=> $title,
				'permalink' => $permalink,
				'text' 		=> $text
			));
		}
		?>
		<div id="main">
			<form action="index.php?action=add" method="POST">
				<input type="text" name="title" placeholder="Title..." required="" />
				<br />
				<input type="text" name="permalink" placeholder="Permalink..." required="" />
				<br />
				<textarea name="text" rows="4" cols="30" placeholder="Text..." required=""></textarea>
				<input type="submit" value="Add Post" />
			</form>
		</div>
		<table>
			<thead>
				<tr>
					<th>
						Post title
					</th>
					<th>
						Permalink
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$posts = $db->prepare("SELECT title, permalink FROM posts ORDER BY id DESC");
				$posts -> execute();
				while($post = $posts -> fetch(PDO::FETCH_ASSOC)){
					echo '
						<tr>
							<td data-column="Post Title">
								'.$post['title'].'
							</td>
							<td data-column="Permalink">
								<a href="/article/'.$post['permalink'].'">'.$post['permalink'].'</a>
							</td>
						</tr>
					';
				}
				?>
			</tbody>
		</table>
	</body>
</html>