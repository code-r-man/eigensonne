
<ul>
<?php

/*
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
});

$app->run();*/

/*
Get the list of all the last 500 stories
*/ 
$storiesListGet = file_get_contents("https://hacker-news.firebaseio.com/v0/topstories.json?print=pretty");

/*
Store the JSON data into an array as object
*/
$storiesList = json_decode($storiesListGet);


for($i=0; $i < 3; $i++) {
	//Get the story contents
	$contGet = file_get_contents("https://hacker-news.firebaseio.com/v0/item/" . $storiesList[0] . ".json?print=pretty");

	// Store story contents as object
	$cont = json_decode($contGet);
	$commentsNo = count($cont->kids);

	?>

	<li id="<?php echo($cont->id);?>">
		<dl>
			<dt><?php echo($i+1);?></dt>
			<dd><?php echo($cont->title);?><span><?php echo($cont->url);?></span></dd>
		</dl>
		<p><?php echo($cont->score);?> points by <?php echo($cont->by); ?> | <?php echo($commentsNo . ' comments');?></p>
		
	</li>
<?php
}

	$contGet = file_get_contents("https://hacker-news.firebaseio.com/v0/item/" . $storiesList[0] . ".json?print=pretty");

	$cont = json_decode($contGet);
	print_r($cont);



// print_r( $storiesListGetLen );
?>

</ul>