{config_load file='smarty.conf'}
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="/img/user2-160x160.jpg" class="/img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{$session.person.name}</p>
        </div>
      </div>

      <!-- search form (Optional) -->
      <form action="/search" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
        </div>
      </form>
      <!-- /.search form -->

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">Menu</li>
        <!-- Optionally, you can add icons to the links -->
        <li class="active"><a href="/"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li class="active"><a href="/controllers"><i class="fa fa-server"></i> <span>Controllers</span></a></li>
        <li class="active"><a href="/people"><i class="fa fa-user"></i> <span>People</span></a></li>
        <li class="active"><a href="/mobiles"><i class="fa fa-mobile"></i> <span>Mobiles</span></a></li>
        <li class="active"><a href="/readers"><i class="fa fa-tablet"></i> <span>Readers</span></a></li>
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>
