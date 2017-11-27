$(function() {
  var urlAdminPrefix = '/admin/';
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
    delButton: function () {
      return this.listPanel.find(".delete-object");
    },
    addButton: function () {
      return this.listPanel.find(".add-object");
    },
    addForm: function () {
      return this.actionPanel.find('.obj-add-form');
    },
    editForm: function () {
      return this.actionPanel.find('.obj-edit-form');
    },
    loader: function () {
      return this.actionPanel.html('<i class="fa fa-spinner font-40 loader" aria-hidden="true"></i>') ;
    },

    initEditActions: function () {
      adminObject.editButton().click(function (e) {
        e.preventDefault();
        adminObject.loader();
        var type = $(this).attr('data-type');
        var id   = $(this).attr('data-id');
        adminObject.actionPanel.load(urlAdminPrefix + type + "/editForm/", {id: id}, function () {
          adminObject.submitEditFormEventListen();
        });
      });
    },

    submitAddFormEventListen: function () {
      this.addForm().submit(function (e) {
        e.preventDefault();
        var form = $(this).serialize();
        adminObject.loader();
        $.post($(this).attr('action'), form, function (data) {
          if (data.err != '') {
            return alert(data.err);
          }
          location.reload();
        }, 'json');
        return false;
      });
    },

    submitEditFormEventListen: function () {
      this.editForm().submit(function (e) {
        e.preventDefault();
        var form = $(this).serialize();
        adminObject.loader();
        $.post($(this).attr('action'), form, function (data) {
          if (data.err != '') {
            return alert(data.err);
          }
          location.reload();
        }, 'json');
        return false;
      });
    }
  };

  adminObject.initEditActions();

  adminObject.addButton().click(function (e) {
    e.preventDefault();
    adminObject.loader();
    var type = $(this).attr('data-type');
    adminObject.actionPanel.load(urlAdminPrefix + type + "/addForm/", {}, function () {
      adminObject.submitAddFormEventListen();
    });
  });

  adminObject.delButton().click(function (e) {
    e.preventDefault();
    if (confirm('Вы действительно хотите удалить?')) {
      adminObject.loader();
      var type = $(this).attr('data-type');
      var id   = $(this).attr('data-id');
      $.post(urlAdminPrefix + type + "/del/", {id: id}, function () {
        location.reload();
      });
    }
  });


  var users = {
    listBlock: function () {
      return adminObject.listPanel.find('.users-list');
    },
    searchText: function () {
      return adminObject.listPanel.find('.users-search');
    },
    search: function () {
      users.listBlock().load(urlAdminPrefix + 'users/search/', {text: users.searchText().val()}, function () {
        adminObject.initEditActions();
      });
    }
  };
  users.search();
  users.searchText().keyup(function () {
    users.search();
  });
});

function adaptiveMenu(window_width) {
  if (window_width < 1000) {
    $('#main_nav').addClass('flex-column').hide();
  }else{
    $('#main_nav').removeClass('flex-column').show();
  }
}
