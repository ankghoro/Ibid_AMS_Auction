<?php 
  foreach ($menu as $key => $value) {
    if($key == "Schedule") {
      $class = ' active';
      $active = false;
    } else {
      $class = '';
    }
    echo '<li class="menu-item'.$class.'">';
      if(!empty($value['submenu'])) {
        echo '<a class="menu-link" href="javascript:void(0);">';
          echo '<span class="title">'.$key.'</span>';
          echo '<span class="arrow"></span>';
        echo '</a>';
        echo '<ul class="menu-submenu">';
          foreach ($value['submenu'] as $submenukey => $submenu) {
            echo '<li class="menu-item">';
              echo '<a class="menu-link" href="'.$submenu.'">';
                echo '<span class="dot"></span>';
                echo '<span class="title">'.$submenukey.'</span>';
              echo '</a>';
            echo '</li>';
          }
        echo '</ul>';
      } else {
        echo '<a class="menu-link" href="'.$value['link'].'">';
          echo '<span class="icon fa '.$value['icon'].'"></span>';
          echo '<span class="title">'.$key.'</span>';
        echo '</a>';
      }
    echo '</li>';
  }
?>