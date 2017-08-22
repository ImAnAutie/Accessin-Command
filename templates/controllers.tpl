{config_load file='smarty.conf'}

	{include file='head.tpl'}
	<title>{#appnamefull#} | Controllers</title>
	<link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.min.css">

</head>

{include file='body_start.tpl'}

  {include file='header.tpl'}
  {include file='left_sidebar.tpl'}

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Controllers
        <small>Manage your organisation's controllers</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/controllers"><i class="fa fa-server"></i> Controllers</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Controllers</h3>
	  <div class="pull-right">
		<a href="/controllers/add" class="btn btn-success"> <i class="fa fa-plus"></i> Add a controller</a>
	  </div>
        </div>
        <div class="box-body">
		<table id="example1" class="table table-bordered table-striped">
                	<thead>
                		<tr>
		                	<th>Name</th>
					<th>Building</th>
					<th>Status</th>
					<th></th>
					<th></th>
		                </tr>
                	</thead>

	                <tbody>
		{foreach from=$controllers item=controller}
        	  		<tr>
		              		<td>{$controller.name}</td>
		              		<td><a href="/buildings/{$controller.building}">{$controller.building_name}</a></td>
		              		<td>{if $controller.setup}{$controller.status}{else}<a href="/controllers/{$controller.id}/setup" class="btn btn-primary"> <i class="fa fa-cog"></i> Setup required</a>{/if}</td>
					<td><a href="/controllers/{$controller.id}/edit" class="btn btn-primary"> <i class="fa fa-edit"></i> Edit</a></td>
					<td><button onclick="showcontrollerdelete({$controller.id});" class="btn btn-danger"> <i class="fa fa-trash"></i> Delete</button></td>
		                </tr>
		{foreachelse}
			<p>No controllers are configured for this organisation.</p>
			<a href="/controllers/add" class="btn btn-success"> <i class="fa fa-plus"></i> Add a controller</a>
		{/foreach}
                </tbody>
                <tfoot>
                <tr>
		        <th>Name</th>
			<th>Building</th>
			<th>Status</th>
			<th></th>
			<th></th>
                </tr>
                </tfoot>
              </table>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

   {include file='footer.tpl'}
</div>
<!-- ./wrapper -->




	<div class="modal modal-danger fade" id="controllerdeletemodal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirm controller deletion</h4>
              </div>
              <div class="modal-body">
                <p>Are you sure you want to delete this controller?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="$('#controllerdeleteform').submit();">DELETE controller</button>
		
		<form id="controllerdeleteform" method="POST" action=""><input type="hidden" name="csrf_token" value="{$csrftoken}"></form>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->


{include file='pre_body_end.tpl'}

<script src="/js/jquery.dataTables.min.js"></script>

<script>
	$('table').DataTable({
			"columns": [
				null,
				null,
				null,
				{ "orderable": false },
				{ "orderable": false }
			]
	});
</script>

<script>
	function showcontrollerdelete(controllerid) {
		$('#controllerdeleteform').attr('action', '/controllers/'+controllerid+"/delete");
		$('#controllerdeletemodal').modal('show');
	};
</script>

{include file='body_end.tpl'}
