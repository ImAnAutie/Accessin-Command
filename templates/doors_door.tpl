{config_load file='smarty.conf'}

	{include file='head.tpl'}
	<title>{#appnamefull#} | {$door.name}</title>
        <link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.min.css">
</head>

{include file='body_start.tpl'}

  {include file='header.tpl'}
  {include file='left_sidebar.tpl'}

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        {$door.name}
        <small>View door configuration and logs</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/doors"><i class="fa fa-sign-in"></i> doors</a></li>
        <li><a href="/doors/{$door.id}"><i class="fa fa-sign-in"></i> {$door.name}</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{$door.name}</h3>
        </div>
        <div class="box-body">
		<span>Name:{$door.name}</span><br />
		<span>Controller:<a href="/controllers/{$controller.id}">{$controller.name}</a></span><br />
		<span>Building:<a href="/buildings/{$building.id}">{$building.name}</a></span><br />

		<br />
		<span><button class="btn btn-success" onclick="unlockdoor({$door.id});">Unlock door</button></span><br />





			<table id="example1" class="table table-bordered table-striped">
                        	<thead>
                        	        <tr>
                        	                <th>Event</th>
                                	        <th>Details</th>
                                	        <th>Trigger system</th>
                                	        <th>Trigger object type</th>
                                	        <th>Trigger object</th>
                                	        <th>Timestamp</th>
                                	</tr>
	                        </thead>
	
        	                <tbody>
                			{foreach from=$doorlogs item=doorlog}
                        		        <tr>
                                        	<td>{$doorlog.event}</td>
                                        	<td>{$doorlog.eventdetails}</td>
                                        	<td>{$doorlog.triggersystem}</td>
                                        	<td>{$doorlog.triggerobjecttype}</td>
                                        	<td><a href="/{$doorlog.triggerobjecttype}/{$doorlog.eventtriggeredby}">{$doorlog.eventtriggeredby}</a></td>
                                        	<td>{$doorlog.timestamp}</td>
	                                </tr>
        	        		{foreachelse}
                	       			<p>No doors are configured for this organisation.</p>
                       	 			<a href="/doors/add" class="btn btn-success"> <i class="fa fa-plus"></i> Add a door</a>
			                {/foreach}
		                </tbody>
                		<tfoot>
                			<tr>
                        	                <th>Event</th>
                                	        <th>Details</th>
                                        	<th>Triggered by</th>
                                        	<th>Timestamp</th>
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

<script>
	function unlockdoor(id) {
		$.get( "/doors/"+id+"/unlock", function(data) {
			data=JSON.parse(data);
			console.log(data);
			if (data.unlocked) {
				alert("Unlocked door.");
			} else {
				alert("Failed to unlock door.");
			};
		}).fail(function(err) {
			console.log(err);
			alert("Failed to unlock door.");
		});
	};
</script>

{include file='pre_body_end.tpl'}
<script src="/js/jquery.dataTables.min.js"></script>

<script>
        $('table').DataTable({
		"order": [[ 5, "desc" ]]
        });
</script>
{include file='body_end.tpl'}
