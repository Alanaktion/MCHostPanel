@extends('layouts.master')

@section('title', 'Log In')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
			<h1>Log In</h1>
			<div class="well">
				<form action="{{ url('/login') }}" method="post">
					{{ csrf_field() }}
					<div class="form-group">
						<label for="username">Username</label>
						<input type="text" class="form-control" id="username" name="username">
					</div>
					<div class="form-group">
						<label for="password">Password</label>
						<input type="password" class="form-control" id="password" name="password">
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">Log In</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
