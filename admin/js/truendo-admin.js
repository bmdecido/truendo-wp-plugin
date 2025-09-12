var decode_entities = (function () {
  // Remove HTML Entities
  var element = document.createElement('div');
  function decode_HTML_entities(str) {
    if (str && typeof str === 'string') {
      // Escape HTML before decoding for HTML Entities
      str = escape(str).replace(/%26/g, '&').replace(/%23/g, '#').replace(/%3B/g, ';');
      element.innerHTML = str;
      if (element.innerText) {
        str = element.innerText;
        element.innerText = '';
      } else {
        // Firefox support
        str = element.textContent;
        element.textContent = '';
      }
    }
    return unescape(str);
  }
  return decode_HTML_entities;
})();

jQuery.decodeEntities = decode_entities;

(function ($) {
  'use strict';

  //start up and make sure things are showing
  $('.truendo_settings_holder .truendo_show_after_hiding.active').slideDown();
  $('.truendo_show_when_active_extra.active').slideDown();
  $('.truendo_show_when_active.active').slideDown();

  //show hide when privacy button is hidden
  $('.truendo_hide_button').change(function () {
    if (this.checked) {
      $('.truendo_show_after_hiding').slideDown();
    } else {
      $('.truendo_show_after_hiding').slideUp();
    }
  });

  //show /hide when truendo is enabled
  $('.truendo_enabled').change(function () {
    if (this.checked) {
      $('.truendo_show_when_active').addClass('active');
      $('.truendo_show_when_active_extra').slideDown('active');
      $('.truendo_submit_holder').addClass('active');
    } else {
      $('.truendo_show_when_active').removeClass('active');
      $('.truendo_show_when_active_extra').removeClass('active');
      $('.truendo_submit_holder').removeClass('active');
    }
  });

  // Select on click
  $('.truendo_settings_holder textarea').on('click', function () {
    $(this).select();
  });

  /*
    // FORM SCAN
    $('.truendo_dash_scan_form').submit(function (e) {
      var cur = e.currentTarget;
      e.preventDefault();
      var url = $(cur).find('.tru_dash_scan_input').val();
      if (url != '') {
        window.open('https://admin.truendo.com/#/wizard?scan=' + url);
      }
    });
  */

  setCurrentTab(0);

  function setCurrentTab(num) {
    var sections = $('.truendo_settings_holder section');
    sections.hide();

    $('.truendo_tab_header').removeClass('active');
    $($('.truendo_settings_holder section')[num]).show();
    $($('.truendo_tab_header')[num]).addClass('active');
  }

  $('.truendo_tab_header').each(function (ind, obj) {
    $(obj).on('click', function (e) {
      e.preventDefault();
      var number = $(this).data('true_tab');
      setCurrentTab(number);
    });
  });

  //REPEATER FIELD
  var $listStat = new lister('statistics', 'tru_stat_', 'Statistics');
  var $listMark = new lister('marketing', 'tru_mark_', 'Marketing');

  $('.truendo_main_form').on('submit', function (e) {
    $listStat.doAddition();
    $listMark.doAddition();
  });

  function lister($type, $prefix, $name) {
    var addButton = document.getElementById($prefix + 'addButton');
    var addInput = document.getElementById($prefix + 'itemInput');
    //console.log($prefix, addInput);
    var me = this;
    var todoList = document.getElementById($prefix + 'todoList');
    var jsonHolder = $('.' + $prefix + 'json_holder');
    var listArray = [];
    //declare addToList function

    function listItemObj(content) {   
      this.content = '';
    }

    var removeItem = function () {
      var parent = this.parentElement.parentElement;
      parent.removeChild(this.parentElement);
      var data = this.parentElement.firstChild.innerText;
      for (var i = 0; i < listArray.length; i++) {
        if (listArray[i].content == data) {
          listArray.splice(i, 1);
          refreshLocal();
          break;
        }
      }
    }
    //function to chage the dom of the list of todo list
    var createItemDom = function (text) {
      var listItem = document.createElement('li');
      var itemLabel = document.createElement('textarea');
      var itemRemoveBtn = document.createElement('button');
      listItem.className = 'form-group';
      itemLabel.innerText = text;
      itemLabel.disabled = true;
      itemRemoveBtn.className = 'btn truendo_remove_button ';
      //itemRemoveBtn.innerText = 'Delete';
      itemRemoveBtn.addEventListener('click', removeItem);
      listItem.appendChild(itemLabel);
      listItem.appendChild(itemRemoveBtn);
      return listItem;
    }
    var refreshLocal = function () {
      var todos = listArray;
      var val = htmlEntities(JSON.stringify(todos));
      jsonHolder.val(val);
    }
    function htmlEntities(str) {
      return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    var addToList = function (e) {
      e.preventDefault();
      me.doAddition();
    }
    this.doAddition = function () {
      if (addInput.value != '') {
        var newItem = new listItemObj();
        newItem.content = addInput.value;
        listArray.push(newItem);
        //add to the local storage
        refreshLocal();
        //change the dom
        var item = createItemDom(addInput.value);
        todoList.appendChild(item);
        addInput.value = '';
      }
    }
    //function to clear todo list array
    var clearList = function () {
      listArray = [];
      //localStorage.removeItem('todoList');
      todoList.innerHTML = '';
    }
    window.addEventListener('load', function () {
      //var list = localStorage.getItem('todoList');
      var list = truendo_local[$prefix + 'header_scripts'];
      if (list != null && list != '') {
        var todos = JSON.parse($.decodeEntities(list));
        listArray = todos;
        for (var i = 0; i < listArray.length; i++) {
          var data = listArray[i].content;
          var item = createItemDom(data);
          todoList.appendChild(item);
        }
      }
    });
    //add an event binder to the button
    if(addButton !== null)
      addButton.addEventListener('click', addToList);
    //	clearButton.addEventListener('click',clearList);
  }
})(jQuery);
