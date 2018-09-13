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
			<article class="article p-3">
				<dl>
					<dt>'./*($i+1).*/'</dt>
					<dd>
						<h6 class="article__title mr-2"><strong>'.$cont->title.'</strong></h6>
						<a href="'.formatURL($cont->url).'" target="_blank" class="article__link-primary">'.formatURL($cont->url).'</a>
					</dd>
				</dl>
				<p class="article__footer">
					<span>'.$cont->score.' points by '.$cont->by.'</span>
					<span><a href="#">hide</a></span>
					<span>'.$commentsNo. ' comments</span>
				</p>
			</article>
		</li>';

		// Add new element to 'story elements' array
		array_push($collection, $el);
	};

	return implode('',$collection);
}

// Create main list (results go here)
function createList($elements) {
	return '<ol class="list-group list-primary">'.$elements.'</ol>';
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
				body {
					background-color: #f4f4f4;
				}

				a {
					color: #e74c3c !important;

					&:hover {
						text-decoration: underline;
					}
				}

				header {
					padding: 2rem 0;
				}

				.list-group-item {
					background-color: #fff;
					padding: 0;
				}

				.article {
					padding-left: 2rem;
				}

				.article > * {
					margin-bottom: 0.3em;
				}

				.article p:last-child {
					margin: 0;
				}

				.article dl dd {
					margin-bottom: 0;
					display: flex;
					justify-content: space-between;
				}

				.article dl dd span {

				}

				.article__title {
					display: inline-block;
					font-weight: 500;
				}

				.article dl dt {
					
				}

				.article__footer {
					font-size: .8em;
				}

				.article__link-primary {
					color: #bbb !important;
					text-decoration: none !important;
				}

				.article__link-primary:hover {
					color: #e74c3c !important;
				}

				.list-primary > li {
					padding-left: 2rem;
				}

				.list-primary > li {
					position: relative;
				}

				.list-primary > li:before {
					counter-increment: list;
					content: counter(list);
					display:inline-block;
					font-weight: 700;
					position: absolute;
					left: 10px;
					top: 0.9rem;
				}

				.list-primary {
					counter-reset: list;
				}
			</style>
			
		').
		'<body>
			<header>
				<div class="container">
					<nav class="nav">
						<div class="row">
							<div class="col">
								<h2><strong>Hacker News</strong></h2>
							</div>
						</div>
					</nav>					
				</div>
			</header>
			<main>
				<div class="container">
					'.createList(createElements()).
					'
				</div>
			</main>
		</body>
	</html>'			
);



// print_r( $storiesListGetLen );

