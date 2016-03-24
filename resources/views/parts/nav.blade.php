<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{ url('/') }}">MCHP</a>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li class="active"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
				<li><a href="{{ url('/plugins') }}">Plugins</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="{{ url('/admin') }}">Administration</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Alan <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="{{ url('/account') }}">Account Settings</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="{{ url('/') }}">Log Out</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</nav>
