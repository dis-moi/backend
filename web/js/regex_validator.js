(function () {
  function validate_url(url) {
    var empty_regex_message = 'L\'url ne peut être vide';
    var invalid_regex_message = 'Cette regexp est invalide';
    var catch_all_regex_msg = 'Cette regexp est trop large';

    //Regex empty
    if (!url) {
      return {success: false, message: empty_regex_message};
    }

    //Regex invalid
    var regex, error;
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
  }

  document.onreadystatechange = function() {
    if (document.readyState === 'complete') {
      var url_selector = "[id$=urlRegex]";
      $('form').on('change', '[id$=urlRegex]', function() {
        var status = validate_url($(this).val());
        if (!status.success) {
          this.setCustomValidity(status.message);
        } else {
          this.setCustomValidity("");
        }
      });
    }
  };
})();