{config_load file='smarty.conf'}

	{include file='head.tpl'}
	<title>{#appnamefull#} | Readers</title>
	<link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.min.css">

</head>

{include file='body_start.tpl'}

  {include file='header.tpl'}
  {include file='left_sidebar.tpl'}

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Readers
        <small>Manage your organisation's readers</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/readers"><i class="fa fa-tablet"></i> Readers</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">readers</h3>
	  <div class="pull-right">
		<a href="/readers/add" class="btn btn-success"> <i class="fa fa-plus"></i> Add a reader</a>
	  </div>
        </div>
        <div class="box-body">
		<table id="example1" class="table table-bordered table-striped">
                	<thead>
                		<tr>
		                	<th>Name</th>
					<th>Door</th>
					<th>Last active</th>
					<th>Status</th>
					<th></th>
					<th></th>
		                </tr>
                	</thead>

	                <tbody>
		{foreach from=$readers item=reader}
        	  		<tr>
		              		<td><a href="/readers/{$reader.id}">{$reader.name}</a></td>
		              		<td><a href="/doors/{$reader.door}">{$reader.door_name}</a></td>
					<td>{if $reader.lastactive ne 0}{$reader.lastactive|date_format:"%D %H:%M:%S"}{/if}</td>
		              		<td>{if $reader.setup}{$reader.status}{else}<a href="/readers/{$reader.id}/setup" class="btn btn-primary"> <i class="fa fa-cog"></i> Setup required</a>{/if}</td>
					<td><a href="/readers/{$reader.id}/edit" class="btn btn-primary"> <i class="fa fa-edit"></i> Edit</a></td>
					<td><button onclick="showreaderdelete({$reader.id});" class="btn btn-danger"> <i class="fa fa-trash"></i> Delete</button></td>
		                </tr>
		{foreachelse}
			<p>No readers are configured for this organisation.</p>
			<a href="/readers/add" class="btn btn-success"> <i class="fa fa-plus"></i> Add a reader</a>
		{/foreach}
                </tbody>
                <tfoot>
                <tr>
		        <th>Name</th>
			<th>door</th>
			<th>Last active</th>
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




	<div class="modal modal-danger fade" id="readerdeletemodal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirm reader deletion</h4>
              </div>
              <div class="modal-body">
                <p>Are you sure you want to delete this reader?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="$('#readerdeleteform').submit();">DELETE reader</button>
		
		<form id="readerdeleteform" method="POST" action=""><input type="hidden" name="csrf_token" value="{$csrftoken}"></form>
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
	function showreaderdelete(readerid) {
		$('#readerdeleteform').attr('action', '/readers/'+readerid+"/delete");
		$('#readerdeletemodal').modal('show');
	};
</script>

{include file='body_end.tpl'}
