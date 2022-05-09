<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />
<title>CPG Toolbox</title>
	<!-- videoJS -->
	<link href="https://vjs.zencdn.net/7.11.4/video-js.css" rel="stylesheet" />
	<link rel="stylesheet" href="css/main.css">
	<!-- jQuery 1.12 -->
	<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
	<!-- TweenMax -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.6.1/gsap.min.js"></script>
	
	<!-- OG Meta Tags -->
	<meta property="og:type" content="website">
	<meta property="og:title" content="">
	<meta property="og:description" content="">
	<meta property="og:image" content="">
	<meta property="og:url" content="">

	<!-- Twitter Meta Tags -->
	<meta name="twitter:title" content="">
	
	<!-- favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
</head>

<body>
	
	<header>
		<a href="#"><img class="logo" src="images/cpg-toolbox-logo.svg"></a>
	</header>
	
	<div class="login">
		<h1>Welcome!</h1>
		<form>
			<label>Email Address</label>
			<input type="text" value="Enter your email address">
			<span id="email-error" class="error-msg">This should be the same email address your invite was sent to</span>
			<label>Password</label>
			<input type="text" value="Enter your password">
			<span id="pw-error" class="error-msg">The password you entered did not match</span>
			<button type="submit">Login <i class="fas fa-angle-double-right"></i></button>
		</form>
	</div>
	
</body>
</html>
