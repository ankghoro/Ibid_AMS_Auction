<!-- Sidebar -->
<aside class="sidebar sidebar-icons-right sidebar-icons-boxed sidebar-expand-lg">
<header class="sidebar-header">
  <a class="logo-icon" href="../index.html"><img src="<?php echo $assets_url; ?>img/logo-icon-light.png" alt="logo icon"></a>
  <span class="logo">
    <a href="../index.html"><img src="<?php echo $assets_url; ?>img/logo-light.png" alt="logo"></a>
  </span>
  <span class="sidebar-toggle-fold"></span>
</header>

<nav class="sidebar-navigation">
  <ul class="menu" id="menu">

    <li class="menu-category">Menu</li>

    <li class="menu-item">
      <a class="menu-link" href="<?php echo $this->config->item('ibid_auth'); ?>">
        <span class="icon fa fa-home"></span>
        <span class="title">Dashboard</span>
      </a>
    </li>

    <li class="menu-item">
      <a class="menu-link" href="<?php echo $this->config->item('ibid_stock'); ?>">
        <span class="icon fa fa-car"></span>
        <span class="title">Stock</span>
      </a>
    </li>
    <li class="menu-item">
    <a class="menu-link" href="<?php echo $this->config->item('ibid_schedule'); ?>">
        <span class="icon fa fa-calendar"></span>
        <span class="title">Schedule</span>
      </a>
    </li>
    <li class="menu-item">
    <a class="menu-link" href="<?php echo $this->config->item('ibid_lot'); ?>">
        <span class="icon fa fa-cubes"></span>
        <span class="title">LOT</span>
      </a>
    </li>
    <li class="menu-item">
    <a class="menu-link" href="<?php echo $this->config->item('ibid_auth'); ?>">
        <span class="icon fa fa-spinner"></span>
        <span class="title">Auto Bidding</span>
      </a>
    </li>
    <li class="menu-item active">
    <a class="menu-link" href="<?php echo $this->config->item('ibid_auction'); ?>">
        <span class="icon fa fa-gavel"></span>
        <span class="title">Auction Management</span>
      </a>
    </li>
    <li class="menu-item">
    <a class="menu-link" href="<?php echo $this->config->item('ibid_kpl'); ?>">
        <span class="icon fa fa-key"></span>
        <span class="title">KPL</span>
      </a>
    </li>
    
    <?php //$this->load->view('partials/theadmin/menu'); ?>

  </ul>
</nav>
</aside>
<!-- END Sidebar -->