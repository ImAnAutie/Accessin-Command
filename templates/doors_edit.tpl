{config_load file='smarty.conf'}

	{include file='head.tpl'}
	<title>{#appnamefull#} | Edit {$door.name}</title>

        <!-- css for select2 fancy dropdown box -->
        <link href="/css/select2.min.css" rel="stylesheet" />

</head>

{include file='body_start.tpl'}

  {include file='header.tpl'}
  {include file='left_sidebar.tpl'}

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Edit door
        <small>Make changes to the door information</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/doors"><i class="fa fa-server"></i> doors</a></li>
        <li><a href="/doors/{$door.id}"><i class="fa fa-server"></i> {$door.name}</a></li>
        <li><a href="/doors/{$door.id}/edit"><i class="fa fa-edit"></i> Edit</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Edit door</h3>
        </div>
        <div class="box-body">



		<form class="form-horizontal" method="POST">
			<fieldset>
				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="name">Name</label>  
				  <div class="col-md-4">
				  <input id="name" name="name" type="text" placeholder="" value="{$door.name}" class="form-control input-md" required="">
				  </div>
				</div>

				<!-- Number input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="relay">relay</label>  
				  <div class="col-md-4">
				  <input id="relay" name="relay" type="number" placeholder="" value="{$door.relay}" class="form-control input-md" required="" min="1">
				  </div>
				</div>

				<!-- Select input-->
                                <div class="form-group">
                                  <label class="col-md-4 control-label" for="controller">Controller</label>
                                  <div class="col-md-4">
                                   <select id="controller" name="controller" class="form-control input-md">

                                        {foreach from=$controllers item=controller}
                                                <option value="{$controller.id}">{$controller.name}</option>
                                        {foreachelse}
                                                <option value="">No controllers are configured for this organisation</option>
                                        {/foreach}


                                   </select>
                                  </div>
                                </div>


				<input type="hidden" name="csrf_token" value="{$csrftoken}">

				<!-- Button (Double) -->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="submit"></label>
				  <div class="col-md-8">
				    <button id="submit" name="submit" class="btn btn-success">Save changes</button>
				    <button id="resetform" name="resetform" class="btn btn-danger" type="reset">Reset form</button>
				  </div>
				</div>
			</fieldset>
		</form>





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

<!-- js for select2 fancy dropdown box -->
<script src="/js/select2.min.js"></script>

<script type="text/javascript">
        $('select').select2({
                "language": {
                        "noResults": function(){
                                return "No controllers found";
                        }
                }
        });
</script>

{include file='body_end.tpl'}
