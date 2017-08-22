{config_load file='smarty.conf'}

	{include file='head.tpl'}
	<title>{#appnamefull#} | Setup {$controller.name}</title>
</head>

{include file='body_start.tpl'}

  {include file='header.tpl'}
  {include file='left_sidebar.tpl'}

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Setup controller
        <small>Configure the controller</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="/controllers"><i class="fa fa-server"></i> Controllers</a></li>
        <li><a href="/controllers/{$controller.id}"><i class="fa fa-server"></i> {$controller.name}</a></li>
        <li><a href="/controllers/{$controller.id}/setup"><i class="fa fa-cog"></i> Setup</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Configuration</h3>
        </div>
        <div class="box-body">
		<div id="wizard_step1">
			<p>To setup your controller complete this wizard, download the configuration file to the controller's SD card and then power on the controller.</p>
			<button onclick="nextstep();" class="btn btn-primary"><i class="fa fa-arrow-right"></i> Start setup</button>
		</div>
		<div id="wizard_step2" hidden>
			<form class="form-horizontal" onsubmit="return false;">
				<fieldset>

					<!-- Multiple Radios (inline) -->
					<div class="form-group">
					  <label class="col-md-4 control-label" for="connectiontype">Connection type</label>
					  <div class="col-md-4"> 
					    <label class="radio-inline" for="connectiontype-0">
					      <input type="radio" name="connectiontype" id="connectiontype-0" value="ethernet" checked="checked">
					      Ethernet
					    </label> 
					    <label class="radio-inline" for="connectiontype-1">
					      <input type="radio" name="connectiontype" id="connectiontype-1" value="wifi">
					      Wifi
					    </label>
					  </div>
					</div>

					<div id="wizard_step2_wifi" hidden>
						<!-- Text input-->
						<div class="form-group">
						  <label class="col-md-4 control-label" for="ssid">SSID</label>  
						  <div class="col-md-4">
						  <input id="ssid" name="ssid" type="text" placeholder="" class="form-control input-md">
						  </div>
						</div>
	
						<!-- Password input-->
						<div class="form-group">
						  <label class="col-md-4 control-label" for="password">Password</label>
						  <div class="col-md-4">
						    <input id="password" name="password" type="password" placeholder="" class="form-control input-md">
						  </div>
						</div>
					</div>

					<!-- Button (Double) -->
					<div class="form-group">
					  <label class="col-md-4 control-label" for="wizard_step1_nextstep"></label>
					  <div class="col-md-8">
					    <button id="wizard_step1_nextstep" name="wizard_step1_nextstep" onclick="nextstep();" class="btn btn-primary"><i class="fa fa-arrow-right"></i> Next</button>
					    <button type="reset" id="wizard_step1_resetform" name="wizard_step1_resetform" class="btn btn-danger">Reset form</button>
					  </div>
					</div>

				</fieldset>
			</form>
		</div>
		<div id="wizard_step3" hidden>
			<span>Please right click (hold down on mobile) and save the configuration download file to the SD card.</span><br />
			<span>Once downloaded, remove the SD card, insert it into the Controller and power it up.</span><br />
			<span>After approximately 5 minutes the controllers status should be showing as READY</span><br /><br />
			<span><i class="fa fa-exclamation" aria-hidden="true" style="font-size:50px;"></i> This file contains sensitive credentials, please save directly to the SD card.</span><br />

			<br /><a id="configurationdownloadlink" class="btn btn-primary" download="ControllerConfiguration.json">Configuration download</a>
		</div>
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



<script>
	wizard_step=1;
	function nextstep() {
		switch (wizard_step) {
			case 1:
				$('#wizard_step1').hide();
				$('#wizard_step2').show();
				wizard_step=2;
				break;

			case 2:
				if($("input[type='radio'][name='connectiontype']:checked").val()=="wifi") {
					if (!$('#ssid').val() || !$('#password').val()) {
						alert("Wifi SSID and password are both required. If using ethernet please select Ethernet as the connection type");
						return false;
					};
				};

				controllerconfiguration={};
				controllerconfiguration.controllerid={$controller.id};
				controllerconfiguration.refreshtoken="{$controller.refreshtoken}";
				controllerconfiguration.connectiontype=$("input[type='radio'][name='connectiontype']:checked").val();

                                if($("input[type='radio'][name='connectiontype']:checked").val()=="wifi") {
					controllerconfiguration.wifi={};
					controllerconfiguration.wifi.ssid=$('#ssid').val();
					controllerconfiguration.wifi.password=$('#password').val();
				};

				$('#configurationdownloadlink').attr('href', 'data:application/json;charset=utf-8,' + encodeURI(JSON.stringify(controllerconfiguration)));
				$('#wizard_step2').hide();
				$('#wizard_step3').show();
				wizard_step=3;
				break;
		};
	};
</script>

<script>
	$(document).ready(function() {
		$("input[type=radio][name=connectiontype]").change(function() {
			console.log(this.value);
			switch (this.value) {
				case "ethernet":
					$('#wizard_step2_wifi').hide();
					break;
				case "wifi":
					$('#wizard_step2_wifi').show();
					break;
			};
		});
	});
</script>


{include file='body_end.tpl'}
