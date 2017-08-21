{config_load file='smarty.conf'}

	{include file='head.tpl'}
	<title>{#appnamefull#} | Add building</title>

	<!-- css for countrycode selection widgit -->
	<link rel="stylesheet" href="/css/countrySelect.min.css">
</head>

{include file='body_start.tpl'}

  {include file='header.tpl'}
  {include file='left_sidebar.tpl'}

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Add a building
        <small>Add a new building to your organisation</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/buildings"><i class="fa fa-buildings"></i> Buildings</a></li>
        <li><a href="/buildings/add"><i class="fa fa-plus"></i> Add</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Add new building</h3>
        </div>
        <div class="box-body">



		<form class="form-horizontal" method="POST">
			<fieldset>

				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="name">Name</label>  
				  <div class="col-md-4">
				  <input id="name" name="name" type="text" placeholder="" class="form-control input-md" required="">
				  </div>
				</div>

				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="line1">Line 1</label>  
				  <div class="col-md-4">
				  <input id="line1" name="line1" type="text" placeholder="" class="form-control input-md" required="">    
				  </div>
				</div>

				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="line2">Line 2</label>  
				  <div class="col-md-4">
				  <input id="line2" name="line2" type="text" placeholder="" class="form-control input-md">    
				  </div>
				</div>
	
				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="city">City</label>  
				  <div class="col-md-4">
				  <input id="city" name="city" type="text" placeholder="" class="form-control input-md" required="">
				  </div>
				</div>

				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="postcode">Postcode</label>  
				  <div class="col-md-4">
				  <input id="postcode" name="postcode" type="text" placeholder="" class="form-control input-md">
				  </div>
				</div>

				<!-- Text input-->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="country">Country</label>  
				  <div class="col-md-4">
				  <input id="country" name="country" type="text" placeholder="" class="form-control input-md">
				  </div>
				</div>

				<input id="country_code" name="country_code" type="hidden">

				<input type="hidden" name="csrf_token" value="{$csrftoken}">

				<!-- Button (Double) -->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="submit"></label>
				  <div class="col-md-8">
				    <button id="submit" name="submit" class="btn btn-success">Add building</button>
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

<script src="/js/countrySelect.min.js"></script>
<script>
	$("#country").countrySelect({
		defaultCountry: "gb",
		responsiveDropdown: true
	});
</script>


{include file='body_end.tpl'}
