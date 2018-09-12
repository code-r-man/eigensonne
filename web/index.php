<?php

/*
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
});

$app->run();*/

// Format the URL
function formatURL($input){
    $input = str_replace('https://','',$input);
    $input = str_replace('http://','',$input);
    $chunks = explode('/',$input);
    
    return $chunks[0];
}

// 

// Get the entries for the main list
function createElements() {
	// Get the list of all the last 500 stories
	$storiesListGet = file_get_contents("https://hacker-news.firebaseio.com/v0/topstories.json?print=pretty");

	// Store the JSON data into an array as object
	$storiesList = json_decode($storiesListGet);

	$collection = [];

	for($i=0; $i < 3; $i++) {
		//Get the story contents
		$contGet = file_get_contents("https://hacker-news.firebaseio.com/v0/item/" . $storiesList[$i] . ".json?print=pretty");

		// Store story contents as object
		$cont = json_decode($contGet);
		$commentsNo = count($cont->kids);


		// Define base HTML element group
		$el = 
		'<li class="list-group-item" id="'.$cont->id.'">
			<dl>
				<dt>'./*($i+1).*/'</dt>
				<dd>'.$cont->title.'<span>'.formatURL($cont->url).'</span></dd>
			</dl>
			<p>
				<span>'.$cont->score.' points by '.$cont->by.'</span>
				<span><a href="#">hide</a></span>
				<span>'.$commentsNo. ' comments</span>
			</p>
		</li>';

		// Add new element to 'story elements' array
		array_push($collection, $el);
	};

	return implode('',$collection);
}

// Create main list (results go here)
function createList($elements) {
	return '<ol class="list-group">'.$elements.'</ol>';
}

// Create head
function createHead($children) {
	return '<head>'.$children.'</head>';
}

echo(
	'<!DOCTYPE html>
	<html lang="en">'.
		createHead('
			<title>Nikola API</title>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<!-- Import "bootrstrap" styles -->
			<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" />
			<!-- Import "CSS normalize" -->
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/modern-normalize/0.5.0/modern-normalize.min.css" />
			<!-- Custom styles -->
			<style>
				.list-main {
					
				}
			</style>
			
		').
		'<body>
			<div class="container">
				<nav class="nav">
					<div class="row">
						<div class="col">

						</div>
					</div>
					</nav>
				'.createList(createElements()).
			'</div>
		</body>
	</html>'			
);



// print_r( $storiesListGetLen );

