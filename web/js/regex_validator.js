(function () {
    class RegExpState {
        constructor(message) {
            this._message = message;
        }
        get message() { return this._message }
    }
    class RegExpStateSuccess extends RegExpState {
        static get success() {
            return true;
        }
    }
    class RegExpStateError extends RegExpState {
        static get success() {
            return false;
        }
    }

    function validate_url_regexp(url_regexp, example_url, loose) {
        const invalid_regex_message = 'Cette regexp est invalide';
        const catch_all_regex_msg = 'Cette regexp est trop large';

        //Regex empty
        if (!url_regexp) {
            // Does not cover required state over here
            // Abort validation though
            return;
        }

        //Regex invalid
        let regex;
        try {
            regex = new RegExp(url_regexp);
        } catch (e) {
            return new RegExpStateError(invalid_regex_message + " (Détails: " + e.message + ")");
        }

        //Regex matching way too many urls
        if (regex.test('//google.com') && regex.test('//lemonde.fr')) {
            return new RegExpStateError(catch_all_regex_msg + " : elle couvre //google.com et //lemonde.fr")
        }

        // Loose verification passed.
        if (loose === true) {
            return new RegExpStateSuccess();
        }

        // Regex matching too many urls
        if (regex.test('//google.com?foo=bar&bar=foo')) {
            return new RegExpStateError(catch_all_regex_msg + " : elle couvre //google.com?foo=bar&bar=foo");
        }
        if (regex.test('//google.com/foo/bar')) {
            return new RegExpStateError(catch_all_regex_msg + " : elle couvre //google.com/foo/bar");
        }

        // Match url example
        if (example_url && !regex.test(example_url)) {
            return new RegExpStateError('Cette regex ne marche pas avec l’exemple ' + example_url);
        }

        return new RegExpStateSuccess();
    }

    function validateFields(regexField, dnField, exampleField) {
        const loose = regexField.id.startsWith('restricted');
        const regexValue = !!dnField.value ?
            dnField.value.replace(/\./g, '\\.') + regexField.value :
            regexField.value;

        const status = validate_url_regexp(
            regexValue,
            exampleField.value,
            loose);

        if (typeof status === 'undefined' || status instanceof RegExpStateSuccess) {
            regexField.setCustomValidity('');
            removeErrorClass(regexField, dnField, exampleField);
        }
        else if (status instanceof RegExpStateError) {
            regexField.setCustomValidity(status.message);
            addErrorClass(regexField, dnField, exampleField);
        }
    }

    function selectFields(child) {
        const parentSelector = '[id*="matchingContexts_"]';
        return [
            jQuery(child).parents(parentSelector).find('[id$="urlRegex"]')[0],
            jQuery(child).parents(parentSelector).find('[id$="domainName"]')[0],
            jQuery(child).parents(parentSelector).find('[id$="exampleUrl"]')[0],
        ];
    }

    function addErrorClass(regexField, dnField, exampleField) {
        const parentSelector = '.field-matching_context .form-group';
        jQuery(regexField).parents(parentSelector).addClass('has-error');
        jQuery(dnField).parents(parentSelector).addClass('has-error');
        jQuery(exampleField).parents(parentSelector).addClass('has-warning');
    }

    function removeErrorClass(regexField, dnField, exampleField) {
        const parentSelector = '.field-matching_context .form-group';
        jQuery(regexField).parents(parentSelector).removeClass('has-error');
        jQuery(dnField).parents(parentSelector).removeClass('has-error');
        jQuery(exampleField).parents(parentSelector).removeClass('has-warning');
    }

    jQuery(($) => {
        $('form [id$="urlRegex"]').change((event) => validateFields(...selectFields(event.target)));
        $('form [id$="exampleUrl"]').change((event) => validateFields(...selectFields(event.target)));
        $('form [id$="domainName"]').change((event) => validateFields(...selectFields(event.target)));
    });
})();