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
				<ul class="article__footer">
					<li>'.$cont->score.' points by <a href="#" class="article__link-alt">'.$cont->by.'</a></li>
					<li><a href="#">hide</a></li>
					<li>'.$commentsNo. ' comments</li>
				</ul>
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
					/*background-color: #f4f4f4;*/
					background-color: #f9fbff;
				}

				a {
					color: #e74c3c !important;

					&:hover {
						text-decoration: underline;
					}
				}

				header {
					padding: 1.5rem 0;
					background-image: linear-gradient(110deg,#04A8FB 1%,#871faf 100%);
					color: #fff;
					margin-bottom: 1.8rem;
					position: fixed;
					top: 0;
					left: 0;
					right: 0;
					z-index: 1000;
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
					position: relative;
					z-index: 10;
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
					padding: 0;
					list-style-type: none;
					display: flex;

				}

				.article__footer li {
					margin-right: 1.4rem;
					position: relative;
					line-height: 1.2em;
				}

				.article__footer li + li:before {
					position: absolute;
					content: "";
					display: block;
					width: 0.35rem;
					height: 0.35rem;
					background-color: #938e8e;
					left: -0.7rem;
					border-radius: 50%;
					top: 0.65em;
					transform: translate(-50%, -50%);
				}

				.article__link-primary {
					color: #bbb !important;
					text-decoration: none !important;
				}

				.article__link-primary:hover {
					color: #e74c3c !important;
				}

				.article__link-alt {
					font-weight: 700;
				}

				.list-primary > li {
					padding-left: 2rem;
				}

				.list-primary > li {
					position: relative;
					overflow: hidden;
					transition: all 0.2s ease-in;
				}

				.list-primary > li:before {
					counter-increment: list;
					content: counter(list)".";
					display:inline-block;
					font-weight: 700;
					position: absolute;
					left: 1rem;
					top: 0.9rem;
					z-index: 10;
				}

				.list-primary > li:after {
					position: absolute;
					content: "";
					display: block;
					width: 114%;
					padding-bottom: 114%;
					left: 50%;
					top: 50%;
					transform: translate(-50%, -50%) scale(0,0);
					background-color: #f5f0f0;
					border-radius: 50%;
					opacity: 0.8;
				}

				.list-primary > li:hover:after {
					transform: translate(-50%, -50%);
					opacity: 1;
					transition: all 0.2s ease-in;
				}

				.list-primary {
					counter-reset: list;
					box-shadow: 0 0 1px 0 rgba(18,32,73,.1), 0 8px 32px 0 rgba(55,92,192,.1);
				}

				.main {
					padding-top: 8rem;
				}

				.nav__menu {
					display: flex;
					align-items: center;
					list-style:none;
					font-weight: 500;
					justify-content: flex-end;
				}

				.nav__menu li a {
					color: #fff !important;
					text-decoration: none !important;
					display: inline-block;
					padding: 1rem 0;
					position: relative;
				}

				.nav__menu li a:before {
					display: block;
					width: 0.5em;
					height: 0.5em;
					background-color: #fff;
					border-radius: 50%;
					position: absolute;
					content:"";
					left: 50%;
					transform: translate(-50%,100%);
					bottom: 0;
					opacity: 0;
					transition: all .25s ease-in;
				}

				.nav__menu li a:hover:before {
					opacity: 1;
					transform: translate(-50%,0);
				}

				.nav__menu li {
					margin-right: 30px;
				}

				.nav__menu li:last-child {
					margin-right: 0;
				}

			</style>
			
		').
		'<body>
			<header>
				<div class="container">
					<nav>
						<div class="row justify-content-between align-items-center">
							<div class="col">
								<h2><strong>Hacker News</strong></h2>
							</div>
							<div class="col">
								<ul class="nav__menu p-0 m-0">
									<li><a href="#">News</a></li>
									<li><a href="#">Comments</a></li>
								</ul>
							</div>
						</div>
					</nav>					
				</div>
			</header>
			<main class="main">
				<div class="container">
					'.createList(createElements()).
					'
				</div>
			</main>
		</body>
	</html>'			
);



// print_r( $storiesListGetLen );

