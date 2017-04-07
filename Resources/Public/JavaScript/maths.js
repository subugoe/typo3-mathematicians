var term = "";
var refresh = false;
var databaseNotAvailable = 'Sorry, the database is currently not available.';

function ow(name) {
  var url = "?eID=mathematicians_ow";
  $(".owResult").html('Loading data ...');
  $.ajax({
    url: url,
    data: {name: name},
    dataType: "html",
    timeout: 10000,
    success: function (data) {
      $(".owResult").html(data);
    },
    error: function (xhr, err, e) {
      var message;
      if (err === 'timeout') {
        message = databaseNotAvailable
      }
      else {
        message = err;
      }
      $(".owResult").replaceWith('<span>' + message + '</span>');
    }
  });
}


function genealogy(name) {
  $("#genResult").html('Loading data ...');
  $.ajax({
    url: "?eID=mathematicians_gen",
    data: {name: name},
    dataType: "html",
    timeout: 8000,
    success: function (data) {
      $("#genResult").html(data);
      return;
    },
    error: function (xhr, err, e) {
      var message;
      if (err === 'timeout') {
        message = databaseNotAvailable
      }
      else {
        message = err;
      }
      $("#genResult").html('<span>' + message + '</span>');
      return;
    }
  });
}


function mactut(name) {
  $.ajax({
    url: "?eID=mathematicians_mactut",
    data: {person: name},
    dataType: "html",
    timeout: 2000,
    success: function (data) {
      $('#mactutResult').html(data)
    },
    error: function (xhr, err, e) {
      alert(err);
      var message;
      if (err === 'timeout') {
        message = databaseNotAvailable
      }
      else {
        message = err;
      }
      $("#macutResult").html('<span>' + message + '</span>');
    }
  });
}


jQuery(document).ready(function () {
  $("#simpleSearch").submit(function (event) {
    event.preventDefault();
    $('#mycarousel').html('<ul class="owResult"></ul>');
    refresh = true;
    term = $("input[name='person']").val();
    genealogy(term);
    ow(term);
  });

});
