(function($)
{
  var typeaheadOptions = {
    ajax: {
      method: 'get',
      url: '/contacts/index.php?perPage=15'
    },
    matcher: function() { return true; },
    sorter: function(items) { return items; },
    updater: function(i)
    {
      var contact = this.ajax.data[i] || {
        id: 0,
        name: ''
      };

      this.$element.next().val(contact.id).change();

      return contact.name;
    },
    itemRenderer: function(i, contact)
    {
      var html = '<em>' + contact.name + '</em>';

      if (contact.company !== '')
      {
        html += '<br>' + contact.company;
      }

      i = $(this.options.item).attr('data-value', i);
      i.find('a').html(html);

      return i[0];
    }
  };

  $.fn.typeaheadContacts = function()
  {
    var typeaheadEl = this.typeahead(typeaheadOptions);

    $(window).resize(function()
    {
      typeaheadEl.data('typeahead').$menu.css('min-width', typeaheadEl.outerWidth());
    });
  };

})(jQuery);
