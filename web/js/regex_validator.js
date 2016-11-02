

var RegexValidator = function() {

  this.validate_url = function(url) {
    var regex_vide = 'L\'url ne peut être vide';
    var regex_invalide = 'Cette regexp est invalide';
    var regex_couvre_trop_d_url = 'Cette regexp est trop large; elle couvre à la fois http://google.com et http://lemonde.fr';

    //Regex empty
    if (url == '') {
      return {success: false, message: regex_vide};
    }

    //Regex invalid
    try {
      regex = new RegExp(url);
    } catch (e) {
      return {success: false, message: regex_invalide};
    }

    //Regex matching too many urls
    if (regex.test('http://google.com') && regex.test('http://lemonde.fr')) {
      return {success: false, message: regex_couvre_trop_d_url};
    }

    return {success: true, message: 'Regex valide'}
  };
};

document.onreadystatechange = function() {
  if (document.readyState === 'complete') {
    var validated_element = $("#recommendation_resource_url");
    validated_element.change(function() {
      var validator = new RegexValidator();
      var status = validator.validate_url($(this).val());
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