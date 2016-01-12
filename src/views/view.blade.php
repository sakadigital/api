<!DOCTYPE html>
<html>
  <head>
	<meta charset="UTF-8">
	<title>{{$content['page_title']}}</title>
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<link rel="stylesheet" type="text/css" href="{{ asset( 'apidoc/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset( 'apidoc/css/bootstrap-theme.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset( 'apidoc/css/AdminLTE.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset( 'apidoc/css/_all-skins.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset( 'apidoc/css/font-awesome.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset( 'apidoc/css/main.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset( 'apidoc/css/jquery.json-viewer.css') }}">
  </head>

<body class="skin-blue sidebar-mini">
	<div class="wrapper">
		<header class="main-header">
			<a href="{{$baseUrl.'/'.Config::get('api.doc_segment')}}" class="logo">
				<span class="logo-mini"><img src="{{ asset('apidoc/img/logo.png') }}"></span>
				<span class="logo-lg"><img src="{{ asset('apidoc/img/logo-saka.png') }}"></span>
			</a>
			<nav class="navbar navbar-static-top" role="navigation">
				<div class="pull-right title">{{Config::get('api.project_name')}} <small>API Documentation</small></div>
				<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">Toggle navigation</span>
				</a>
			</nav>
		</header>

		<aside class="main-sidebar">
			<section class="sidebar">
				<ul class="sidebar-menu">
					@if(Config::get('api.version'))
					<li class="header">Api versions</li>
					<li>
						<div style="padding:10px;">
							<div class="dropdown">
								<button class="btn btn-default btn-block dropdown-toggle" type="button" data-toggle="dropdown" style="text-align:left">Version {{$content['current_version']}} <span class="caret" style="float:right;margin:10px 0"></span></button>
								<ul class="dropdown-menu" style="width:100%">
									@foreach(Config::get('api.version') as $key => $val)
										@if(Config::get('api.version.'.$key.'.enabled'))
										<?php
											$current_state = '';
											if ($state['controller'] ==! '') $current_state .= '/'.$state['controller'];
											if ($state['function'] ==! '') $current_state .= '/'.$state['function'];
										?>
										<li><a href="{{ URL::to(Config::get('api.prefix').'/'.Config::get('api.version.'.$key.'.prefix')).'/'.Config::get('api.documentation_prefix').$current_state}}" style="color:#333">Version {{$key}}</a></li>
										@endif
									@endforeach
								</ul>
							</div>
						</div>
					</li>
					@endif
					<li class="header">API Objects</li>

					@foreach($menu as $key => $val)
					<li class="{{$val['active']?'active':''}} treeview">
						<a href="{{$val['object_url']}}">
							<i class="fa fa-file-text-o"></i> <span>{{$key}}</span> <i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
							@foreach($val['functions'] as $k => $function)
							@if ($k !== 'namespace')
							<?php
								switch ($function['method']) {
									case 'GET':
										$color = 'success';
									break;
									case 'POST':
										$color = 'warning';
									break;
									case 'PUT':
										$color = 'info';
									break;
									case 'DELETE':
										$color = 'danger';
									break;									
									default:
										$color = 'default';
									break;
								}
							?>
							<li {{$function['active']?'class=active':''}}><a href="{{$function['doc_uri']}}"><span class="label label-{{$color}}">{{$function['method']}}</span> {{$function['name']}}</a></li>
							@endif
							@endforeach
						</ul>
					</li>
					@endforeach
				</ul>
			</section>
		</aside>

	  <!-- Content Wrapper. Contains page content -->
	  <div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			@if($content['type']=='function')
			<h1>{{strtoupper($content['data']['method'])}} <small>{{$content['data']['uri']}}</small></h1>
			<ol class="breadcrumb">
				<li><a href="{{$baseUrl.'/'.Config::get('api.documentation_prefix')}}"><i class="fa fa-home"></i> Home</a></li>
				<li><a href="{{$content['data']['object_uri']}}"><i class="fa fa-file-text-o"></i> {{ucwords($content['data']['object'])}}</a></li>
				<li class="active">{{$content['data']['name']}}</li>
			</ol>
			@elseif($content['type']=='home')
			<h1>{{Config::get('api.project_name')}} <small>Api Documentation</small></h1>
			<ol class="breadcrumb">
				<li><a href="{{$baseUrl.'/'.Config::get('api.documentation_prefix')}}"><i class="fa fa-home"></i> Home</a></li>
			</ol>
			@else
				<h1><small>{{$content['body_title']}}</small></h1>	
				<ol class="breadcrumb">
					<li><a href="{{$baseUrl.'/'.Config::get('api.documentation_prefix')}}"><i class="fa fa-home"></i> Home</a></li>
					<li class="active"><i class="fa fa-file-text-o"></i> {{$content['body_title']}}</li>
				</ol>
			@endif
		</section>

		<section class="content">
			<p>{{$content['description']}}</p>
			@if($content['type'] === 'function')
				<div class="row">
					<div class="col-sm-6">					
						<div class="callout callout-info">
							<h4>Resource URL</h4>
							<p>{{$content['data']['api_uri']}}</p>
						</div>

						@if(!is_null($content['data']['property']['param']))
						<div class="box box-info">
							<div class="box-header">
								<h3 class="box-title">Parameter</h3>
							</div>
							<div class="box-body no-padding">
								<table class="table table-hover">
								<tr>
									<th width="20%">Parameter</th>
									<th>Description</th>
									<th>Validation Role</th>
								</tr>
								@foreach($content['data']['property']['param'] as $key => $var)
								<tr>
									<td><code>{{$var['param']}}</code></td>
									<td>{{$var['description']}}</td>
									<td>{!!$var['role'] !== '' ? '<code>'.$var['role'].'</code>':''!!}</td>
								</tr>
								@endforeach
								</table>
							</div>
						</div>
						@endif

						@if (isset($content['data']['property']['return']))
						<div class="box box-success">
							<div class="box-header">
								<h3 class="box-title">Return</h3>
							</div>
							<div class="box-body no-padding">
								<table class="table table-bordered">
								<tr>
									<th width="20%">Variable</th>
									<th>Description</th>
								</tr>
								@foreach($content['data']['property']['return'] as $key=>$var)
								<tr>
									<td><code>{{$var['return']}}</code></td>
									<td>{{$var['description']}}</td>
								</tr>
								@endforeach
								</table>
							</div>
						</div>
						@endif

						@if(isset($content['data']['property']['error']))
						<div class="box box-danger">
							<div class="box-header">
								<h3 class="box-title">Error</h3>
							</div>
							<div class="box-body no-padding">
								<table class="table table-bordered">
								<tr>
									<th width="50%">Message</th>
									<th>Description</th>
								</tr>
								@foreach($content['data']['property']['error'] as $key=>$var)
								<tr>
									<td><code>{{$var['error']}}</code></td>
									<td>{{$var['description']}}</td>
								</tr>
								@endforeach
								</table>
							</div>
						</div>
						@endif
					</div>
					<div class="col-sm-6">
						<div class="box box-info">
							<div class="box-header ui-sortable-handle" style="cursor: move;">
								<i class="fa fa-rocket"></i>
								<h3 class="box-title">Test method</h3>
							</div>
			                <div class="box-body">
								<form action="#" method="post">
								<input type="hidden" name="method" value="{{$content['data']['method']}}">
								<input type="hidden" name="url" value="{{$content['data']['api_uri']}}">
								@if(!is_null($content['data']['property']['param']))
									@foreach($content['data']['property']['param'] as $key => $var)
									<div class="form-group">
										<label for="{{$var['param']}}" class="col-sm-4 control-label">{{$var['param']}}</label>
										<div class="col-sm-8">
											<input type="text" class="form-control input-param" id="{{$var['param']}}">
										</div>
									</div>
									@endforeach
								@endif
								</form>
			                </div>
			                <div class="box-footer clearfix">
								<button class="pull-right btn btn-default" id="send">Send <i class="fa fa-send"></i></button>
			                </div>
						</div>
						<div class="box box-success collapsed-box">
							<div class="box-header">
								<h3 class="box-title">Result Data</h3>
								<div class="pull-right box-tools">
									<button id="btnCollapse" class="btn btn-success btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
								</div>
							</div>
							<div class="box-body">
								<pre class="result-data" style="padding-left:20px"><i>No result</i></pre>
							</div>
						</div>
					</div>
				</div>
			@elseif($content['type'] === 'class')
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">Available method</h3>
						</div>
						<div class="box-body no-padding">
							<table class="table table-hover">
								<tr>
									<th width="20%">Name</th>
									<th width="5%">Method</th>
									<th width="70%">Url</th>
									<th width="5%"><i class="fa fa-eye"></i></th>
								</tr>
								@foreach($content['data'] as $function)
									@if(is_array($function))
									<tr>
										<td>{{$function['name']}}</td>
										<td>{{$function['method']}}</td>
										<td>{{$function['api_uri']}}</td>
										<td><a class="btn btn-default btn-xs" href="{{$function['doc_uri']}}"><i class="fa fa-arrow-right"></i></a></td>
									</tr>
									@endif
								@endforeach
							</table>
						</div>
					</div>
				</p>
			@endif
		</section>

		<!-- Main content -->
		<section class="content">
		</section><!-- /.content -->

	  </div><!-- /.content-wrapper -->
	</div><!-- ./wrapper -->

	<!-- jQuery 2.1.4 -->
	<script src="{{ asset('apidoc/js/jquery.min.js') }}"></script>
	<script src="{{ asset('apidoc/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('apidoc/js/app.min.js') }}" type="text/javascript"></script>    
	<script src="{{ asset('apidoc/js/jquery.json-viewer.js') }}" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#send').click(function(){
			var button = $(this);
			button.html('Sending <i class="fa fa-spinner fa-spin">');
			var box = $(this).closest('.box');
			var method = box.find('input[name=method]').val();
			var url = box.find('input[name=url]').val();
			var input ={};
			box.find('input:visible').each(function(){
				input[$(this).attr('id')] = $(this).val();
			});

			$.ajax({
				url : url,
				type : method,
				data : input,
				success : function(data){
					$('.result-data').jsonViewer(data);
					operResult(button);
				},
				error : function(data){
					$('.result-data').jsonViewer(JSON.parse(data.responseText));
					operResult(button);
				}
			})
		});

		function operResult(button)
		{
			button.html('Send <i class="fa fa-send">');
			var btn = $('#btnCollapse');
			if (btn.closest('.box').hasClass('collapsed-box'))
			{
				btn.trigger('click');
			}
		}
	});
	</script>
  </body>
</html>
