(function () {
  validate_url = function(url) {
    var empty_regex_message = 'L\'url ne peut être vide';
    var invalid_regex_message = 'Cette regexp est invalide';
    var catch_all_regex_msg = 'Cette regexp est trop large';

    //Regex empty
    if (!url) {
      return {success: false, message: empty_regex_message};
    }

    //Regex invalid
    try {
      regex = new RegExp(url);
    } catch (e) {
      error = invalid_regex_message + " (Détails: " + e.message + ")";
      return {success: false, message: error};
    }

    //Regex matching too many urls
    var state = {success: true};
    if (regex.test('http://google.com') && regex.test('http://lemonde.fr')) {
      state.success = false;
      state.message = catch_all_regex_msg + " : elle couvre http://google.com et http://lemonde.fr";
    } else if (regex.test('http://google.com?foo=bar&bar=foo')) {
      state.success = false;
      state.message = catch_all_regex_msg + " : elle couvre http://google.com?foo=bar&bar=foo";
    } else if (regex.test('http://google.com/foo/bar')) {
      state.success = false;
      state.message = catch_all_regex_msg + " : elle couvre http://google.com/foo/bar";
    }
    if (!state.success) {
      return state;
    }

    return {success: true, message: 'Regex valide'}
  };

  document.onreadystatechange = function() {
    if (document.readyState === 'complete') {
      var validated_elements = $("#recommendation_resource_url");
      validated_elements.change(function() {
        var status = validate_url($(this).val());
        remove_error_message($(this));
        if (!status.success) {
          show_error_message($(this), status.message);
        }
      });
    }
  };

  var show_error_message = function(element, message) {
    element.parent().addClass('has-error').append(
      '<div class="error-block"><span class="label label-danger">Erreur</span>'
      +message
      +'</div>'
    )
  };

  var remove_error_message = function(element) {
    element.parent().removeClass('has-error');
    element.siblings('.error-block').remove();
  };

})();