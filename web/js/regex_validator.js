(function () {
  validate_url = function(url) {
    var empty_regex_message = 'L\'url ne peut être vide';
    var invalid_regex_message = 'Cette regexp est invalide';
    var catch_all_regex_msg = 'Cette regexp est trop large; elle couvre à la fois http://google.com et http://lemonde.fr';

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
    if (regex.test('http://google.com') && regex.test('http://lemonde.fr')) {
      return {success: false, message: catch_all_regex_msg};
    }

    return {success: true, message: 'Regex valide'}
  };

  document.onreadystatechange = function() {
    if (document.readyState === 'complete') {
      var validated_element = $("#recommendation_resource_url");
      validated_element.change(function() {
        var status = validate_url($(this).val());
        remove_error_message(validated_element);
        if (!status.success) {
          show_error_message(validated_element, status.message);
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