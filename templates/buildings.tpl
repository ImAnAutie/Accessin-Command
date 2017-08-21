{config_load file='smarty.conf'}

	{include file='head.tpl'}
	<title>{#appnamefull#} | Buildings</title>
	<link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.min.css">

</head>

{include file='body_start.tpl'}

  {include file='header.tpl'}
  {include file='left_sidebar.tpl'}

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Buildings
        <small>Manage your organisation's buildings</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/buildings"><i class="fa fa-buildings"></i> Buildings</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Buildings</h3>
	  <div class="pull-right">
		<a href="/buildings/add" class="btn btn-success"> <i class="fa fa-plus"></i> Add a building</a>
	  </div>
        </div>
        <div class="box-body">
		<table id="example1" class="table table-bordered table-striped">
                	<thead>
                		<tr>
		                	<th>Name</th>
					<th>Line 1</th>
					<th>Line 2</th>
					<th>City</th>
					<th>Postcode</th>
					<th>Country</th>
					<th>Status</th>
					<th></th>
					<th></th>
		                </tr>
                	</thead>

	                <tbody>
		{foreach from=$buildings item=building}
        	  		<tr>
		              		<td>{$building.name}</td>
		              		<td>{$building.line1}</td>
		              		<td>{$building.line2}</td>
		              		<td>{$building.city}</td>
		              		<td>{$building.postcode}</td>
		              		<td>{$building.country_name}</td>
		              		<td>{$building.status}</td>
					<td><a href="/buildings/{$building.id}/edit" class="btn btn-primary"> <i class="fa fa-edit"></i> Edit</a></td>
					<td><button onclick="showbuildingdelete({$building.id});" class="btn btn-danger"> <i class="fa fa-trash"></i> Delete</a></td>
		                </tr>
		{foreachelse}
			<p>No buildings are configured for this organisation.</p>
			<a href="/buildings/add" class="btn btn-success"> <i class="fa fa-plus"></i> Add a building</a>
		{/foreach}
                </tbody>
                <tfoot>
                <tr>
                	<th>Name</th>
			<th>Line 1</th>
			<th>Line 2</th>
			<th>City</th>
			<th>Postcode</th>
			<th>Country</th>
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




	<div class="modal modal-danger fade" id="buildingdeletemodal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirm building deletion</h4>
              </div>
              <div class="modal-body">
                <p>Are you sure you want to delete this building?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="$('#buildingdeleteform').submit();">DELETE building</button>
		
		<form id="buildingdeleteform" method="POST" action=""><input type="hidden" name="csrf_token" value="{$csrftoken}"></form>
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
				null,
				null,
				null,
				null,
				{ "orderable": false },
				{ "orderable": false }
			]
	});
</script>

<script>
	function showbuildingdelete(buildingid) {
		$('#buildingdeleteform').attr('action', '/buildings/'+buildingid+"/delete");
		$('#buildingdeletemodal').modal('show');
	};
</script>

{include file='body_end.tpl'}
