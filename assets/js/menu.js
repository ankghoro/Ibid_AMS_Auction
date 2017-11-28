$( document ).ready(function() {
  hostname = "http://"+window.location.hostname+"/";
  active = '';
  $.ajax({
    type: "GET",
    url: "http://ibid-ams.dev/api/menu",
    dataType: "json",
    success: function(data){
      jQuery.each( data.menu, function( i, val ) {
        active = (hostname === val.link) ? ' active' : '';
        if (val.submenu.length > 0) {
          $('ul#menu').append('<li class="menu-item'+active+'">'+
                                '<a class="menu-link" href="javascript:void(0);">'+
                                  '<span class="title">'+i+'</span>'+
                                  '<span class="arrow"></span>'+
                                '</a>'+
                                '<ul class="menu-submenu" id="submenu_'+i+'""></ul>'+
                              '</li>'
                              );
          jQuery.each( val.submenu, function( is, vals ) {
            $('ul#submenu_'+i).append('<li class="menu-item">'+
                                        '<a class="menu-link" href="'+is+'">'+
                                          '<span class="dot"></span>'+
                                          '<span class="title">'+vals+'</span>'+
                                        '</a>'+
                                      '</li>'
                                      );
          });
        } else {
          $('ul#menu').append('<li class="menu-item'+active+'">'+
                                '<a class="menu-link" href="'+val.link+'">'+
                                  '<span class="icon fa '+val.icon+'"></span>'+
                                  '<span class="title">'+i+'</span>'+
                                '</a>'+
                              '</li>'
                              );
        }
      });
    },
    error: function(response) {
      console.log(response); 
    },
  });
});