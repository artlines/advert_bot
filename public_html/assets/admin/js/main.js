$(function() {
  var window_width = $(window).width();
  adaptiveMenu(window_width);

  $(window).resize(function(){
    window_width = $(window).width();
    adaptiveMenu(window_width);
  });

  $('#menu_button').click(function(){
    $('#main_nav').slideToggle();
  });

  var adminObject = {
    actionPanel: $("#action-panel"),
    listPanel: $("#list-panel"),
    editButton: function () {
      return this.listPanel.find(".edit-object");
    },
    addButton: function () {
      return this.listPanel.find(".add-object");
    },
    loader: function () {
      return this.actionPanel.html('<i class="fa fa-spinner font-40 loader" aria-hidden="true"></i>') ;
    }
  };

  adminObject.editButton().click(function (e) {
    e.preventDefault();
    var type = $(this).attr('data-type');
    var id   = $(this).attr('data-id');
    adminObject.actionPanel.load("/admin/" + type + "/edit/" + id);
  });

  adminObject.addButton().click(function (e) {
    e.preventDefault();
    adminObject.loader();
    var type = $(this).attr('data-type');
    adminObject.actionPanel.load("/admin/" + type + "/addForm/");
  });
});

function adaptiveMenu(window_width) {
  if (window_width < 1000) {
    $('#main_nav').addClass('flex-column').hide();
  }else{
    $('#main_nav').removeClass('flex-column').show();
  }
}