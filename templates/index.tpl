{config_load file='smarty.conf'}

{include file='head.tpl'}
<title>{#appnamefull#} | Dashboard</title>

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

      <!--------------------------
        | Your Page Content Here |
        -------------------------->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

   {include file='footer.tpl'}
</div>
<!-- ./wrapper -->

{include file='pre_body_end.tpl'}
{include file='body_end.tpl'}
