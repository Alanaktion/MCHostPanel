<?php
require_once 'inc/lib.php';

session_start();
if (empty($_SESSION['user']) || !$user = user_info($_SESSION['user'])) {
	// Not logged in, redirect to login page
	header('Location: .');
	exit('Not Authorized');
}

?><!doctype html>
<html>
<head>
<title>File Manager | MCHostPanel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
<link rel="stylesheet" href="css/smooth.css" id="smooth-css">
<link rel="stylesheet" href="css/style.css">
<meta name="author" content="Alan Hardman <alan@phpizza.com>">
<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">

$(document).ready(function () {

	// Directory tree item click
	$('#dirtree').on('click', 'a', function () {

		// Adjust styles
		$('#dirtree a').parents('li').removeClass('active');
		$('#dirtree a i').addClass('icon-folder-close').removeClass('icon-folder-open');

		$(this).parents('li').addClass('active');
		$(this).children('i').removeClass('icon-folder-close').addClass('icon-folder-open');

		// Load directory
		loaddir($(this).attr('href'));

		// Prevent navigation
		return false;
	});

	// File list item click
	$('#filelist').on('click', 'a', function (e) {

		// If not holding Ctrl or Shift, clear selection
		if (!e.ctrlKey && !e.shiftKey)
			$('#filelist a').parents('li').removeClass('active');

		// Add or remove from selection
		$(this).parents('li').toggleClass('active');

		// Enable/disable Edti and Delete buttons
		if ($('#filelist li.active').length == 1) {
			$('#btn-delete,#btn-edit,#btn-rename,#btn-dl,#btn-view').prop('disabled', false);
		} else if ($('#filelist li.active').length > 1) {
			$('#btn-delete').prop('disabled', false);
			$('#btn-edit,#btn-rename,#btn-dl,#btn-view').prop('disabled', true);
		} else {
			$('#btn-delete,#btn-edit,#btn-rename,#btn-dl,#btn-view').prop('disabled', true);
		}

		// Prevent navigation
		return false;
	});

	// File list item double click
	$('#filelist').on('dblclick', 'a', function () {

		// Load directory
		if ($(this).data('type') == 'dir')
			loaddir($(this).attr('href'));

		// Open file
		if ($(this).data('type') == 'file')
			window.location = 'edit.php?file=' + encodeURIComponent($(this).attr('href'));

	});

	// Clear selection on Esc
	$(document).on('keyup', function (e) {
		if (e.which == 27 || e.keyCode == 27 || e.charCode == 27) {
			$('#filelist li.active').removeClass('active');
			$('#btn-delete,#btn-edit,#btn-rename,#btn-dl,#btn-view').prop('disabled', true);
		}
	});

	// Add delete button handler
	$('#btn-delete').click(function () {
		// Get files
		window.selectedfiles = [];
		$('#filelist li.active').each(function () {
			window.selectedfiles.push($(this).children('a').attr('href'));
		});

		// Delete if confirmed
		if (confirm('Are you sure you want to delete the selected files?')) {
			$.post('ajax.php', {
				req: 'delete',
				files: window.selectedfiles
			},function (data) {
				loaddir(window.lastdir);
			}).error(function () {
				alert('There was an error deleting your files.');
			});
		}
	});

	// Add edit button handler
	$('#btn-edit').click(function () {
		window.location = 'edit.php?file=' + encodeURIComponent($('#filelist li.active a').attr('href'));
	});

	// Add rename button handler
	$('#btn-rename').click(function () {
		newname = prompt('Enter a new name for the file:', basename($('#filelist li.active a').attr('href')));
		if (newname) {
			$.post('ajax.php', {
				req: 'rename',
				path: $('#filelist li.active a').attr('href'),
				newname: newname
			},function (data) {
				loaddir(window.lastdir);
			}).error(function () {
				alert('There was an error deleting your files.');
			});
		}
	});

	// Add view button handler
	$('#btn-view').click(function () {
		window.open('download.php?dl=0&file=' + encodeURIComponent($('#filelist li.active a').attr('href')));
	});

	// Add download button handler
	$('#btn-dl').click(function () {
		window.open('download.php?dl=1&file=' + encodeURIComponent($('#filelist li.active a').attr('href')));
	});

	// Add upload button handler
	$('#btn-upload').click(function () {
		$('#modal-upload').modal('show');
	});

	// Generate button tooltips
	$('button.ht').tooltip();

	// Load requested directory
	loaddir('<?php echo $_GET["dir"] ? $_GET["dir"] : '/'; ?>');

});

function loaddir(dir) {
	window.lastdir = dir;

	// Clear the file list
	$('#filelist').empty().addClass('loading');
	$('#btn-delete,#btn-edit,#btn-rename,#btn-dl,#btn-view').prop('disabled', true);
	$('#dirtree li:gt(2)').remove();

	// Load the directory contents
	$.post('ajax.php', {
		req: 'dir',
		dir: dir
	},function (data) {

		// Calculate path components
		var lvl_array = window.lastdir.replace(/\/$/, '').split('/');

		// Add the header breadcrumbs
		$('#path').empty();
		var lvl_current = '/';
		for (var i = 0; i < lvl_array.length; i++) {
			if (i) {
				lvl_current += lvl_array[i] + '/';
				$('#path').append('<button type="button" class="btn" onclick="loaddir(\'' + lvl_current + '\')">' + lvl_array[i] + '</button>');
			} else
				$('#path').append('<button type="button" class="btn" onclick="loaddir(\'/\')"><i class="icon-home"></i></button>');
		}

		// Add directory tree nodes
		var dirtree = '';
		//for(var i; i < lvl_count - 1; i++)
		//dirtree+= '<li><a href="'+window.lastdir.replace(/\/$/,'')+'/'+lvl_array[i]+'"><i class="icon-folder-close"></i> '+lvl_array[i]+'</a></li>';

		// Add items to the directory tree and file list
		var filelist = '';
		for (var d in data.dirs) {
			dirtree += '<li><a href="' + window.lastdir.replace(/\/$/, '') + '/' + data.dirs[d] + '"><i class="icon-folder-close"></i> ' + data.dirs[d] + '</a></li>';
			//filelist+='<li><a href="'+window.lastdir.replace(/\/$/,'')+'/'+data.dirs[d]+'" data-type="dir"><i class="icon-folder-close"></i> '+data.dirs[d]+'</a></li>';
		}
		for (var f in data.files) {
			filelist += '<li><a href="' + window.lastdir.replace(/\/$/, '') + '/' + data.files[f] + '" data-type="file"><i class="icon-file"></i> ' + data.files[f] + ' <small class="pull-right">' + size_format(data.sizes[f]) + '</small><div class="clearfix"></div></a></li>';
		}

		// Add directory contents to document
		$('#dirtree').append(dirtree);
		$('#filelist').removeClass('loading').html(filelist);

		// Select current directory
		$('#dirtree li.active').removeClass('active');
		//$('#dirtree [href="'+window.lastdir+'"]').addClass('active');
		if (window.lastdir == '/')
			$('#home').addClass('active');

		// Change upload directory
		$('#iframe-upload').attr('src', 'uploader.php?dir=' + encodeURIComponent(window.lastdir));

	}, 'json').error(function () {
		try {
			console.log('Error loading directory "' + window.lastdir + '"');
		} catch (ex) {}
	});
}

function size_format(s) {
	if (s >= 1073741824)
		s = Math.round(s / 1073741824 * 100) / 100 + ' GB';
	else if (s >= 1048576)
		s = Math.round(s / 1048576 * 100) / 100 + ' MB';
	else if (s >= 1024)
		s = Math.round(s / 1024 * 100) / 100 + ' KB';
	else
		s = s + ' bytes';
	return s;
}

function basename(path, suffix) {
	var b = path.replace(/^.*[\/\\]/g, '');
	if (typeof(suffix) == 'string' && b.substr(b.length - suffix.length) == suffix)
		b = b.substr(0, b.length - suffix.length);
	return b;
}
</script>
</head>
<body>
<?php require 'inc/top.php'; ?>
<div class="tab-content">
	<div class="tab-pane active">
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span3">
					<div class="well sidebar-nav">
						<ul class="nav nav-list" id="dirtree">
							<li class="nav-header">Directories</li>
							<li class="active" id="home"><a href="/"><span class="icon-home"></span> Home</a></li>
							<li class="divider"></li>
						</ul>
					</div>
				</div>
				<div class="span9">
					<div class="well">
						<div class="row-fluid">
							<p class="span6 btn-group" id="path"></p>
							<div class="span6 btn-toolbar" style="margin-top:0;text-align:right;">
								<div class="btn-group">
									<button id="btn-delete" type="button" class="btn ht" title="Delete" disabled><i class="icon-trash"></i></button>
									<button id="btn-edit" type="button" class="btn ht" title="Edit" disabled><i class="icon-edit"></i></button>
									<button id="btn-rename" type="button" class="btn ht" title="Rename" disabled><i class="icon-repeat"></i></button>
								</div>
								<div class="btn-group">
									<button id="btn-view" type="button" class="btn ht" title="View" disabled><i class="icon-picture"></i></button>
									<button id="btn-dl" type="button" class="btn ht" title="Download" disabled><i class="icon-download"></i></button>
								</div>
								<button id="btn-upload" type="button" class="btn btn-primary"><i class="icon-upload icon-white"></i> Upload</button>
							</div>
						</div>
						<ul class="nav nav-list" id="filelist"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade hide" id="modal-upload" tabindex="-1" role="dialog" aria-labelledby="lbl-upload" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="lbl-upload">Upload Files</h3>
	</div>
	<div class="modal-body">
		<iframe src="uploader.php" id="iframe-upload" border="0" frameborder="0" style="width:100%;height:125px;" allowtransparency="true"></iframe>
	</div>
</div>
</body>
</html>
