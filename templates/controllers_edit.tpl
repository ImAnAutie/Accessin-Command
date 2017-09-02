{config_load file='smarty.conf'}

	{include file='head.tpl'}
	<title>{#appnamefull#} | Edit {$controller.name}</title>

        <!-- css for select2 fancy dropdown box -->
        <link href="/css/select2.min.css" rel="stylesheet" />

</head>

{include file='body_start.tpl'}

  {include file='header.tpl'}
  {include file='left_sidebar.tpl'}

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Edit controller
        <small>Make changes to the controller information</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/controllers"><i class="fa fa-server"></i> Controllers</a></li>
        <li><a href="/controllers/{$controller.id}"><i class="fa fa-server"></i> {$controller.name}</a></li>
        <li><a href="/controllers/{$controller.id}/edit"><i class="fa fa-edit"></i> Edit</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Edit controller</h3>
        </div>
        <div class="box-body">



		<form class="form-horizontal" method="POST">
			<fieldset>
				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="name">Name</label>  
				  <div class="col-md-4">
				  <input id="name" name="name" type="text" placeholder="" value="{$controller.name}" class="form-control input-md" required="">
				  </div>
				</div>

				<!-- Select input-->
                                <div class="form-group">
                                  <label class="col-md-4 control-label" for="building">Building</label>
                                  <div class="col-md-4">
                                   <select id="building" name="building" class="form-control input-md">

                                        {foreach from=$buildings item=building}
                                                <option value="{$building.id}">{$building.name} ({$building.city}-{$building.country_name})</option>
                                        {foreachelse}
                                                <option value="">No buildings are configured for this organisation</option>
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
                                return "No buildings found";
                        }
                }
        });
</script>

{include file='body_end.tpl'}
