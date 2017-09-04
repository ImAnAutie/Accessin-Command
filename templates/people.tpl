{config_load file='smarty.conf'}

	{include file='head.tpl'}
	<title>{#appnamefull#} | People</title>
	<link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.min.css">

</head>

{include file='body_start.tpl'}

  {include file='header.tpl'}
  {include file='left_sidebar.tpl'}

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        People
        <small>Manage your organisation's people</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/people"><i class="fa fa-user"></i> People</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">People</h3>
	  <div class="pull-right">
		<a href="/people/add" class="btn btn-success"> <i class="fa fa-plus"></i> Add a person</a>
	  </div>
        </div>
        <div class="box-body">
		<table id="example1" class="table table-bordered table-striped">
                	<thead>
                		<tr>
		                	<th>Name</th>
					<th>Email</th>
					<th>Last active</th>
					<th>Status</th>
					<th></th>
					<th></th>
		                </tr>
                	</thead>

	                <tbody>
		{foreach from=$people item=person}
        	  		<tr>
		              		<td><a href="/people/{$person.id}">{$person.name}</a></td>
		              		<td><a href="mailto:{$person.email}">{$person.email}</a></td>
                                        <td>{if $person.lastactive ne 0}{$person.lastactive|date_format:"%D %H:%M:%S"}{/if}</td>
                                        <td><span title="{$person.status_description}">{$person.status_name}</span></td>
					<td><a href="/people/{$person.id}/edit" class="btn btn-primary"> <i class="fa fa-edit"></i> Edit</a></td>
					<td><button onclick="showpersondelete({$person.id});" class="btn btn-danger"> <i class="fa fa-trash"></i> Delete</button></td>
		                </tr>
		{foreachelse}
			<p>No people are configured for this organisation.</p>
			<a href="/people/add" class="btn btn-success"> <i class="fa fa-plus"></i> Add a person</a>
		{/foreach}
                </tbody>
                <tfoot>
                <tr>
                	<th>Name</th>
			<th>Email</th>
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




	<div class="modal modal-danger fade" id="persondeletemodal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Confirm person deletion</h4>
              </div>
              <div class="modal-body">
                <p>Are you sure you want to delete this person?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="$('#persondeleteform').submit();">DELETE person</button>
		
		<form id="persondeleteform" method="POST" action=""><input type="hidden" name="csrf_token" value="{$csrftoken}"></form>
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
	function showpersondelete(personid) {
		$('#persondeleteform').attr('action', '/people/'+personid+"/delete");
		$('#persondeletemodal').modal('show');
	};
</script>

{include file='body_end.tpl'}
