{config_load file='smarty.conf'}

	{include file='head.tpl'}
	<title>{#appnamefull#} | Add Person</title>

	<!-- css for select2 fancy dropdown box -->
	<link href="/css/select2.min.css" rel="stylesheet" />

</head>

{include file='body_start.tpl'}

  {include file='header.tpl'}
  {include file='left_sidebar.tpl'}

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Add a person
        <small>Add a new person to your organisation</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/people"><i class="fa fa-user"></i> Person</a></li>
        <li><a href="/people/add"><i class="fa fa-plus"></i> Add</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Add new person</h3>
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
				  <label class="col-md-4 control-label" for="email">Email</label>  
				  <div class="col-md-4">
				  <input id="email" name="email" type="email" placeholder="" class="form-control input-md" required="">
				  </div>
				</div>

				<input type="hidden" name="csrf_token" value="{$csrftoken}">

				<!-- Button (Double) -->
				<div class="form-group">
				  <label class="col-md-4 control-label" for="submit"></label>
				  <div class="col-md-8">
				    <button id="submit" name="submit" class="btn btn-success">Add person</button>
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

<!-- css for countrycode selection widgit -->
<script src="/js/countrySelect.min.js"></script>

<script>
	$("#country").countrySelect({
		defaultCountry: "gb",
		responsiveDropdown: true
	});
</script>



<!-- js for select2 fancy dropdown box -->
<script src="/js/select2.min.js"></script>

<script type="text/javascript">
	$('select').select2({
		"language": {
			"noResults": function(){
				return "No doors found";
			}
		}
	});
</script>


{include file='body_end.tpl'}
