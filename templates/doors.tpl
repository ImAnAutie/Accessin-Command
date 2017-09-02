{config_load file='smarty.conf'}

	{include file='head.tpl'}
	<title>{#appnamefull#} | Doors</title>
	<link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.min.css">

</head>

{include file='body_start.tpl'}

  {include file='header.tpl'}
  {include file='left_sidebar.tpl'}

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Doors
        <small>Manage your organisation's doors</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/doors"><i class="fa fa-sign-in"></i> Doors</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Doors</h3>
	  <div class="pull-right">
		<a href="/doors/add" class="btn btn-success"> <i class="fa fa-plus"></i> Add a door</a>
	  </div>
        </div>
        <div class="box-body">
		<table id="example1" class="table table-bordered table-striped">
                	<thead>
                		<tr>
		                	<th>Name</th>
					<th>Controller</th>
					<th>Relay</th>
					<th>Building</th>
					<th></th>
					<th></th>
		                </tr>
                	</thead>

	                <tbody>
		{foreach from=$doors item=door}
        	  		<tr>
		              		<td><a href="/doors/{$door.id}">{$door.name}</a></td>
		              		<td><a href="/controllers/{$door.controller}">{$door.controller_name}</a></td>
					<td>{if $door.relay eq 0}Invalid relay-0{else}{$door.relay}{/if}</td>
		              		<td><a href="/buildings/{$door.building}">{$door.building_name}</a></td>
					<td><a href="/doors/{$door.id}/edit" class="btn btn-primary"> <i class="fa fa-edit"></i> Edit</a></td>
					<td><button onclick="showdoordelete({$door.id});" class="btn btn-danger"> <i class="fa fa-trash"></i> Delete</button></td>
		                </tr>
		{foreachelse}
			<p>No doors are configured for this organisation.</p>
			<a href="/doors/add" class="btn btn-success"> <i class="fa fa-plus"></i> Add a door</a>
		{/foreach}
                </tbody>
                <tfoot>
                <tr>
                	<th>Name</th>
			<th>Controller</th>
			<th>Relay</th>
			<th>Building</th>
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




	<div class="modal modal-danger fade" id="doordeletemodal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirm door deletion</h4>
              </div>
              <div class="modal-body">
                <p>Are you sure you want to delete this door?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="$('#doordeleteform').submit();">DELETE door</button>
		
		<form id="doordeleteform" method="POST" action=""><input type="hidden" name="csrf_token" value="{$csrftoken}"></form>
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
	function showdoordelete(doorid) {
		$('#doordeleteform').attr('action', '/doors/'+doorid+"/delete");
		$('#doordeletemodal').modal('show');
	};
</script>

{include file='body_end.tpl'}
