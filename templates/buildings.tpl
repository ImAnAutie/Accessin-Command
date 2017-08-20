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
					<th></th>
		                </tr>
                	</thead>

	                <tbody>
		{foreach from=$buildings item=building}
        	  		<tr>
		              		<td>{$building.name}</td>
					<td><a href="/buildings/{$building.id}/edit" class="btn btn-primary"> <i class="fa fa-edit"></i> Edit</a></td>
		                </tr>
		{foreachelse}
			<p>No buildings are configured for this organisation.</p>
			<a href="/buildings/add" class="btn btn-success"> <i class="fa fa-plus"></i> Add a building</a>
		{/foreach}
                </tbody>
                <tfoot>
                <tr>
                  <th>Name</th>
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



{include file='pre_body_end.tpl'}

<script src="/js/jquery.dataTables.min.js"></script>

<script>
	$('table').DataTable();
</script>

{include file='body_end.tpl'}
