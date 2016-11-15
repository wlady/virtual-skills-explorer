<html>
	<head>
		<title>Virtual Skills Explorer</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAcHgjNvGOTDbGWc-3q2IbrDYo1ppkzlfQ&signed_in=true"></script>
		<script src="{{ elixir('js/scripts.js') }}"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="{{ elixir('css/custom.css') }}">
	</head>
	<body>
	<nav class="navbar navbar-default">
		<div class="container"><h3 class="title">Virtual Skills Explorer</h3></div>
	</nav>
		<div class="container">
			@if (count($skills['list'])>0)
			<div class="form_content">
				<form class="skills" onsubmit="return false">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="from" value="0">
					<fieldset>
						<label class="control-label">Skills</label>
						@foreach($skills['list'] as $skill)
							<div class="checkbox">
								<label>
									<input type="checkbox" name="skills[]" value="{{ $skill['key'] }}" />
									{{ $skill['key'] }}
									<bb>({{ $skill['doc_count'] }})</bb>
								</label>
							</div>
						@endforeach
					</fieldset>
					<fieldset>
						<label class="control-label">Sort By</label>
						<select class="form-control" name="sort">
							<option value="">Not Sorted</option>
							<option value="registered">Date</option>
						</select>
					</fieldset>
					<fieldset>
						<label class="control-label">Sort Direction</label>
						<select class="form-control" name="dir" disabled>
							<option value="asc">Up</option>
							<option value="desc">Down</option>
						</select>
					</fieldset>
					<fieldset>
						<label class="control-label">Page Size</label>
						<select class="form-control" name="size">
							<option>10</option>
							<option>20</option>
							<option>30</option>
							<option>40</option>
							<option selected>50</option>
						</select>
					</fieldset>
					<div class="btn_div">
						<input type="reset" value="Reset" class="btn btn-default" />
						<button name="get-filtered" class="btn btn-success">Show</button>
					</div>
				</form>
			</div>
			<div class="content">
				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#dashboard">Dashboard</a></li>
					<li><a data-toggle="tab" href="#list">Hits</a></li>
					<li><a data-toggle="tab" href="#charts">Skills</a></li>
					<li><a data-toggle="tab" href="#map">Places</a></li>
				</ul>
				<div class="tab-content">
					<div id="dashboard" class="tab-pane fade in active">
						<div class="container">
							<h4 class="text-muted">Total records: {{ $total['count'] }}</h4>
							<p>All data are fictitious.</p>
						</div>
					</div>
					<div id="list" class="tab-pane fade">
						<div class="total"></div>
						<ul class="pagination"></ul>
						<div class="list"></div>
						<ul class="pagination"></ul>
					</div>
					<div id="charts" class="tab-pane fade">
						<div class="total"></div>
						<ul class="pagination"></ul>
						<div class="charts-container"></div>
					</div>
					<div id="map" class="tab-pane fade">
						<div class="total"></div>
						<ul class="pagination"></ul>
						<div id="gmap"></div>
					</div>
				</div>
			</div>
			@else
				<p class="bg-danger">Skills were not found</p>
			@endif
		</div>
	</body>
	<script type="text/html" id="template">
		<div class="list_item">
			<div><label>Name</label><span data-content="name"></span></div>
			<div><label>City</label><span data-content="city"></span></div>
			<div><label>Time Zone</label><span data-content="timezone"></span></div>
			<div><label>Date</label><span data-content="registered"></span></div>
			<div><label>IP</label><span data-content="ip"></span></div>
			<div><label>Skills</label><span data-content="skills" data-format="SkillFormatter"></span></div>
		</div>
	</script>

</html>
