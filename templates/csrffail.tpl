{config_load file='smarty.conf'}

{include file='head.tpl'}
<title>{#appnamefull#} | Error</title>

{include file='body_start.tpl'}

  {include file='header.tpl'}
  {include file='left_sidebar.tpl'}

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Dashboard
        <small>Setup and operate your Access Control system</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li>
<!--        <li><a href="/"><i class="fa fa-dashboard"></i> Dashboard</a></li> -->
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <div class="error-page">
        <h2 class="headline text-red">403</h2>

        <div class="error-content">
          <h3><i class="fa fa-warning text-red"></i> Oops! Something went wrong.</h3>
          <p>
	  	Please ensure you do not have multiple tabs with {#appnamefull#} open
          	You may want to <a href="/">return to the dashboard</a>
          </p>
	  <br />
	  <p>Error code:<b>CSRFFAIL</b></p>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

   {include file='footer.tpl'}
</div>
<!-- ./wrapper -->

{include file='pre_body_end.tpl'}
{include file='body_end.tpl'}
