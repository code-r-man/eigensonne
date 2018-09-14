<?php


// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/api/{type}', function ($type) use ($app) {
	return createDoc($type);
});

// Redirect to homepage
$app->get('/web', function () use ($app) {
    return $app->redirect('/api/top');
});

$app->run();

// Format the URL
function formatURL($input){
    $input = str_replace('https://','',$input);
    $input = str_replace('http://','',$input);
    $chunks = explode('/',$input);
    
    return $chunks[0];
}

// Time difference (minutes, hours, days)
function timeDiff($event) {

	// Current timestamp
	$now = time();

	// Diff. in now and event in seconds 
	$passed = $now - $event;

	$days = round($passed / 86400);

	$hours = round($passed / 3600);

	$minutes = round($passed / 60);

	// Add 's' suffix for mutliples
	$plural = '';

	// Output
	$output = 'more than 30 days ago';

	if ($minutes < 60) {
	    if ($minutes > 1) {
	        $plural = 's';
	    }
	    $output = $minutes . ' minute' . $plural .' ago';

	} else if ($hours < 24) {
	    if ($hours > 1) {
	        $plural = 's';
	    }
	    $output = $hours . ' hour' . $plural .' ago';
	} else if ($days < 30) {
	    if ($days > 1) {
	        $plural = 's';
	    }
	    $output = $days . ' day' . $plural .' ago';
	}

	return $output;
}


// Get the entries for the main list
function createElements($type) {
	// Get the list of all the last 500 stories
	$storiesListGet = file_get_contents("https://hacker-news.firebaseio.com/v0/".$type."stories.json?print=pretty");

	// Store the JSON data into an array as object
	$storiesList = json_decode($storiesListGet);

	$collection = [];
	$el = '';

	for($i=0; $i < 5; $i++) {
		//Get the story contents
		$contGet = file_get_contents("https://hacker-news.firebaseio.com/v0/item/" . $storiesList[$i] . ".json?print=pretty");

		// Store story contents as object
		$cont = json_decode($contGet);

		// Define base HTML element group

		// Structure for 'topstories' - default
		if ($type === 'top') {
			$el = 
			'<li class="list-group-item" id="'.$cont->id.'">
				<article class="article p-3">
					<dl>
						<dt></dt>
						<dd>
							<h6 class="article__title mr-2"><strong>'.$cont->title.'</strong></h6>
							<a href="'.formatURL($cont->url).'" target="_blank" class="article__link-primary">'.formatURL($cont->url).'</a>
						</dd>
					</dl>
					<ul class="article__footer">
						<li>'
							.$cont->score.' points by <a href="#fake" class="article__link-alt">'.$cont->by.'</a>
							 '.timeDiff($cont->time).'
						</li>
						<li><a href="#fake">hide</a></li>
						<li><a href="#fake">'.count($cont->kids).' comments</a></li>
					</ul>
				</article>
			</li>';
		} else if ($type === 'new') {
			$el = 
			'<li class="list-group-item" id="'.$cont->id.'">
				<article class="article p-3">
					<dl>
						<dt></dt>
						<dd>
							<h6 class="article__title mr-2"><strong>'.$cont->title.'</strong></h6>
							<a href="'.formatURL($cont->url).'" target="_blank" class="article__link-primary">'.formatURL($cont->url).'</a>
						</dd>
					</dl>
					<ul class="article__footer">
						<li>'
							.$cont->score.' points by <a href="#fake" class="article__link-alt">'.$cont->by.'</a>
							 '.timeDiff($cont->time).'
						</li>
						<li><a href="#fake">hide</a></li>
						<li><a href="#fake">past</a></li>
						<li><a href="#fake">web</a></li>
						<li><a href="#fake">discuss</a></li>
					</ul>
				</article>
			</li>';			
		} else if ($type === 'show') {
			$el = 
			'<li class="list-group-item" id="'.$cont->id.'">
				<article class="article p-3">
					<dl>
						<dt></dt>
						<dd>
							<h6 class="article__title mr-2"><strong>'.$cont->title.'</strong></h6>
							<a href="'.formatURL($cont->url).'" target="_blank" class="article__link-primary">'.formatURL($cont->url).'</a>
						</dd>
					</dl>
					<ul class="article__footer">
						<li>'
							.$cont->score.' points by <a href="#fake" class="article__link-alt">'.$cont->by.'</a>
							 '.timeDiff($cont->time).'
						</li>
						<li><a href="#fake">discuss</a></li>
					</ul>
				</article>
			</li>';						
		} else if ($type === 'ask') {
			$el = 
			'<li class="list-group-item" id="'.$cont->id.'">
				<article class="article p-3">
					<dl>
						<dt></dt>
						<dd>
							<h6 class="article__title mr-2"><strong>'.$cont->title.'</strong></h6>
						</dd>
					</dl>
					<ul class="article__footer">
						<li>'
							.$cont->score.' points by <a href="#fake" class="article__link-alt">'.$cont->by.'</a>
							 '.timeDiff($cont->time).'
						</li>
						<li><a href="#fake">discuss</a></li>
					</ul>
				</article>
			</li>';					
		} else if ($type === 'job') {
			$el = 
			'<li class="list-group-item" id="'.$cont->id.'">
				<article class="article p-3">
					<dl>
						<dt></dt>
						<dd>
							<h6 class="article__title mr-2"><strong>'.$cont->title.'</strong></h6>
							<a href="'.formatURL($cont->url).'" target="_blank" class="article__link-primary">'.formatURL($cont->url).'</a>
						</dd>
					</dl>
					<ul class="article__footer">
						<li>
							<a href="#fake">'.timeDiff($cont->time).'</a>
						</li>
					</ul>
				</article>
			</li>';
		} else {
		}

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

// Create Document

function createDoc($type) {
	return
		'<!DOCTYPE html>
		<html lang="en">'.
			createHead('
				<title>Nikola API</title>
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<!-- Import "CSS normalize" -->
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/modern-normalize/0.5.0/modern-normalize.min.css" />
				<!-- Import "bootrstrap" styles -->
				<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" />
				<!-- Custom styles -->
				<style>
					body {
						background-color: #f9fbff;
					}

					.overlay {
						position: fixed;
						top: 0;
						left: 0;
						bottom: 0;
						right: 0;
						background-color: #000;
						opacity: .6;
						z-index: 30;
						visibility: hidden;
						transition: all .3 ease-in;
					}

					.overlay.in {
						visibility: visible;
					}

					@media (min-width: 768px) {
						display:none;
					}
	
					a {
						color: #e74c3c !important;
	
						&:hover {
							text-decoration: underline;
						}
					}
	
					.btn-primary {
						user-select: none;
						box-shadow: 0 4px 8px 0 rgba(18,74,138,.24);
						background: #79d634;
						color: #fff;
						text-decoration: none !important;
						padding: 0.5rem 3rem !important;
						border-radius: 0.2rem;
						font-weight: 700;
						transition: all .3s ease-in;
						display: inline-block !important;
					}
	
					.btn-primary:hover {
						background: #6cc828;
					}
	
					.btn-primary:before {
						display: none !important;
					}
	
					.nav__main > * {
						text-decoration: none !important;
						color: #fff !important;
						transition: all .3s ease-in;
						display: inline-block;
						font-size: 1.28rem;
						
					}

					@media (min-width: 480px) {
						.nav__main > * {
							font-size: 1.6rem;
						}
					}

					@media (min-width: 992px) {
						.nav__main > * {
							font-size: 2rem;
						}
					}
	
					.nav__main:hover {
						opacity: .65;
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
						flex-direction: column;
					}

					@media (min-width: 768px) {
						.article dl dd {
							flex-direction: row;
						}
					}

					.article dl dd a {
						text-align: right;
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
						flex-wrap: wrap;
					}
	
					.article__footer li {
						margin-right: 1.4rem;
						position: relative;
						line-height: 1.2em;
					}

					.article__footer li:first-child {
						width: 100%;
						margin-bottom: 0.3rem;
					}

					@media (min-width: 760px) {
						.article__footer li:first-child {
							width: auto;
							margin-bottom: 0;
						}
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

					.article__footer li:first-child + li:before {
						display: none;
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
						position: relative;
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
	
					.list-primary > li:hover {
						background-color: #f5f0f0;
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
						background-color: #34495e;
						position: absolute;
						flex-direction: column;
						left:0;
						top: 100%;
						min-width: 300px;
						height: calc(100vh - 86px);
						transition: all .3s ease-in;
						transform: translateX(-100%);
					}

					.nav__menu.in {
						transform: none;
					}

					@media (min-width: 768px) {
						.nav__menu {
							position: static;
							flex-direction: row;
							justify-content: flex-end;
							background-color: transparent;
							height: auto;
							transform: none;
						}
					}

					.nav__base {
						position: static;
						text-align: right;
					}

					@media (min-width: 768px) {
						position: relative;
					}
	
					.nav__menu li a {
						color: #fff !important;
						text-decoration: none !important;
						display: block;
						padding: 1rem 0;
						position: relative;
						text-align: center;
					}
					
					@media (max-width: 767px) {
						.nav__menu li a:hover {
							background-color: #4c6b8a;
						}
					}
					

					@media (min-width: 768px) {
						.nav__menu li a {
							display: inline-block;
							text-align: left;
						}
					}
	
					.nav__menu li a:before {
						display: none;
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

					@media (min-width: 768px) {
						.nav__menu li a:before {
							display: block;
						}
					}
	
					.nav__menu li a:hover:before {
						opacity: 1;
						transform: translate(-50%,0);
					}
					

					.nav__menu li {
						width: 100%;
						text-align: center;
						border-bottom: 1px solid #000;
					}

					@media (min-width: 768px) {
						.nav__menu li {
							margin-right: 30px;
							text-align: left;
							border-bottom: none;
						}
					}
					
					.nav__menu li:last-child {
						margin-right: 0;
						border-bottom: none;
						padding-top: 1rem;
					}
					
					@media (min-width: 768px) {
						.nav__menu li:last-child {
							padding-top: 0;
						}
					}


					.nav__toggle {
						padding: 0;
						background-color: transparent !important;
						display: inline-block;
						padding: 10px;
						border: none !important;
						border-radius: 0 !important;
						outline: none !important;
					}

					@media (min-width: 768px) {
						.nav__toggle {
							display: none;
						}
					}

					.nav__toggle span {
						display: block;
						height: 2px;
						width: 20px;
						background-color: #fff;
					}

					.nav__toggle span + span {
						margin-top: 3px;
					}
	
				</style>	
			').
			'<body>
				<div class="overlay js-overlay"></div>
				<header>
					<div class="container">
						<nav>
							<div class="row justify-content-between align-items-center">
								<div class="col">
									<a class="nav__main" href="/api/top"><h2><strong>Hacker News</strong></h2></a>
								</div>
								<div class="col nav__base">
									<button class="nav__toggle js-nav-toggle">
										<span></span>
										<span></span>
										<span></span>
									</button>
									<ul class="nav__menu p-0 m-0 js-nav-menu">
										<li><a href="/api/new">News</a></li>
										<li><a href="#fake">Comments</a></li>
										<li><a href="/api/show">Show</a></li>
										<li><a href="/api/ask">Ask</a></li>
										<li><a href="/api/job">Jobs</a></li>
										<li><a href="#fake" class="btn-primary">Login</a></li>
									</ul>
								</div>
							</div>
						</nav>					
					</div>
				</header>
				<main class="main">
					<div class="container">
						'.createList(createElements($type)).
						'
					</div>
				</main>
				<!-- jQuery -->
				<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>

				<!-- Main JS -->
				<script>
					$(\'[href="#fake"]\').click(function(event){
						event.preventDefault();
						alert("Dummy link pressed. No navigation here, sorry.");
					});
					
					// Toggle nav menu
					$(".js-nav-toggle").click(function(){
						$(".js-nav-menu, .js-overlay").toggleClass("in");
					});

					// Close nav menu
					$(".js-overlay").click(function(){
						$(".js-nav-menu, .js-overlay").removeClass("in");
					});

				</script>
			</body>
		</html>';			
}

// print_r( $storiesListGetLen );

