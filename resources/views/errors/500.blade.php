
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>500 Internal Server Error | {{ ucfirst($companyInfo->name) }}</title>
	<meta content="{{ ucfirst($companyInfo->brief) }}" name="description" />
	<meta content="{{ ucfirst($companyInfo->name) }}" name="author" />
	<!-- App favicon -->
	<link rel="shortcut icon" href="{{ asset('admintheme/images/favicon.ico') }}">

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Cabin:400,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Montserrat:900" rel="stylesheet">

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="{{ asset('css/style.css') }}" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

</head>

<body>

	<div id="notfound">
		<div class="notfound">
			<div class="notfound-404">
				<h3>Oops! Internal Server Error</h3>
				<h1><span class="text-orange">5</span><span class="text-blue">0</span><span class="text-orange">0</span></h1>
			</div>
			<h2>Why not try refreshing your page? or you can contact Lini Support</h2>
			<a href="javascript:history.back()" class="tombol-kembali">go back</a>
		</div>
	</div>

</body>

</html>
