$(document).ready(function() {

  $(".cl-vnavigation li ul").each(function() {
    $(this).parent().addClass("parent");
  });

  $(".cl-vnavigation").delegate(".parent > a", "click", function(e) {
    var ul = $(this).parent().find("ul");
    ul.slideToggle(300, 'swing', function() {
      var p = $(this).parent();
      if (p.hasClass("open")) {
        p.removeClass("open");
      } else {
        p.addClass("open");
      }
    });
    e.preventDefault();
  });

  $(".cl-toggle").click(function(e) {
    var ul = $(".cl-vnavigation");
    ul.slideToggle(300, 'swing', function() {
    });
    e.preventDefault();
  });

  var link_prefix = $('#link_prefix').val();

  $('.btn_list').click(function() {
    var action = link_prefix + '/';
    location.href = action;
    return false;
  });

  $('.btn_save').click(function() {
    var action = link_prefix + '/save/';
    location.href = action;
    return false;
  });

  $('.btn_edit').click(function() {
    var id = $(this).parents('tr').attr('id');
    var action = link_prefix + '/save/' + id + '/';
    location.href = action;
    return false;
  });

  $('.btn_twitter_send').click(function() {
    var id = $(this).parents('tr').attr('id');
    var name = $(this).parents('tr').attr('name');

    location.href = '/admin/tweetSave/?section=' + name + '&id=' + id;

    return false;
  });

  $('.btn_twitter_view').click(function() {
    var id = $(this).parents('tr').attr('id');
    var name = $(this).parents('tr').attr('name');

    location.href = '/admin/tweet/?section=' + name + '&id=' + id;

    return false;
  });

  $('.btn_facebook_send').click(function() {
    var id = $(this).parents('tr').attr('id');
    var name = $(this).parents('tr').attr('name');

    location.href = '/admin/facepostSave/?section=' + name + '&id=' + id;

    return false;
  });

  $('.btn_facebook_view').click(function() {
    var id = $(this).parents('tr').attr('id');
    var name = $(this).parents('tr').attr('name');

    location.href = '/admin/facepost/?section=' + name + '&id=' + id;

    return false;
  });

  $('.setActive').change(function() {
    var active = ($(this).is(':checked')) ? '1' : '0';
    var action = '/admin/active/save/';
    var id = $(this).parents('tr').attr('id');
    var name = $(this).parents('tr').attr('name');

    $.post(action, {
      active: active,
      id: id,
      name: name
    });
  });

  $('.trash_re').click(function() {

    if ($('.delete').is(':checked') !== true) {
      alert('Não há nenhum ítem selecionado.');
      return;
    }

    var form = newForm();

    $('.delete:checked').each(function(i) {
      var id = $(this).parents('tr').attr('id');
      var name = $(this).parents('tr').attr('name');
      form.append('<input type="hidden" name="itens[' + i + '][id]" value="' + id + '" />');
      form.append('<input type="hidden" name="itens[' + i + '][name]" value="' + name + '" />');
    });

    var action = link_prefix + '_restore/';

    form.attr('action', action);
    form.submit();
  });


  $('.trash_ex').click(function() {

    if ($('.delete').is(':checked') !== true) {
      alert('Não há nenhum ítem selecionado.');
      return;
    }

    var msg = 'Deseja realmente excluir os ítens selecionados?';
    if ($('.delete:checked').length === 1) {
      msg = 'Deseja realmente excluir o item selecionado?';
    }

    var c = confirm(msg);

    if (c) {
      var form = newForm();

      $('.delete:checked').each(function(i) {
        var id = $(this).parents('tr').attr('id');
        var name = $(this).parents('tr').attr('name');
        form.append('<input type="hidden" name="itens[' + i + '][id]" value="' + id + '" />');
        form.append('<input type="hidden" name="itens[' + i + '][name]" value="' + name + '" />');
      });

      if (link_prefix == '/admin/trash') {
        var action = link_prefix + '_delete/';
      } else {
        var action = link_prefix + '/delete/';
      }


      form.attr('action', action);
      form.submit();
    }
  });

  $('.drop_sequence').change(function() {
    var sequence = $(this).val();
    var id = $(this).attr('id');
    var name = $(this).parents('tr').attr('name');
    var link = '/admin/sequence/';

    var form = newForm();

    form.append('<input type="hidden" name="name" value="' + name + '" />');
    form.append('<input type="hidden" name="sequence" value="' + sequence + '" />');
    form.append('<input type="hidden" name="id" value="' + id + '" />');
    form.attr('action', link);

    form.submit();
  });

  $('.ajax_intinify_cat').change(function() {
    var id = $(this).val();

    $('.drop_infinity_line.other').remove();
    $('.drop_infinity_line').hide();

    if (id != '0') {
      var exclude_id = $('#id').val();
      var url = link_prefix + '_infinity_cat/';

      $.get(url, {
        id_page_cat: id,
        exclude_id: exclude_id
      }, function(data) {
        $('.drop_infinity_container').html(data);
        $('.drop_infinity_line').show();
        intinity_event();
      });
    }
  });

  intinity_event();

  function intinity_event()
  {
    $('.ajax_infinity').unbind();
    $('.ajax_infinity').change(function() {
      var id = $(this).val();
      var duplication_container = $(this).parents('.drop_infinity_line');

      duplication_container.nextAll('.drop_infinity_line').remove();
      if (id != '0') {
        var exclude_id = $('#id').val();
        var url = link_prefix + '_infinity/';

        $.get(url, {
          id_parent: id,
          exclude_id: exclude_id
        }, function(data) {
          var clone = duplication_container.clone();
          clone.find('.drop_infinity_container').html(data);
          clone.addClass('other');

          duplication_container.after(clone);

          intinity_event();
        });
      }
    });

  }

});

function newForm()
{
  return $('<form method="POST"></form>').appendTo('body');
}