(function () {

    const listOfIdSelectors = [
        'urlRegex',
        'domainName',
        'exampleUrl',
        'excludeUrlRegex',
    ].map((_) => `[id$="${_}"]`);

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
    class ExcludeRegExpStateError extends RegExpStateError {}

    function validate_url_regexp(url_regexp, exclude_url_regex, example_url, loose) {
        const catch_all_regex_msg = 'Cette regexp est trop large';

        //Regex empty
        if (!url_regexp) {
            // Does not cover required state over here
            // Abort validation though
            return;
        }

        //Regex invalid
        let regex, exclude_regex;
        try {
            regex = new RegExp(url_regexp, 'i');
        } catch (e) {
            return new RegExpStateError('Cette regexp est invalide' + " (Détails: " + e.message + ")");
        }
        try {
            exclude_regex = exclude_url_regex && new RegExp(exclude_url_regex, 'i');
        } catch (e) {
            return new ExcludeRegExpStateError('Cette regexp est invalide' + " (Détails: " + e.message + ")");
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

        // Exclude url does not match url example
        if (example_url && exclude_regex && exclude_regex.test(example_url)) {
            return new ExcludeRegExpStateError('Cette regex d’exclusion ne devrait pas matcher l’exemple ' + example_url);
        }

        return new RegExpStateSuccess();
    }

    function validateFields(regexField, dnField, exampleField, excludeField) {
        const loose = regexField.id.startsWith('restricted');
        const regexValue = !!dnField.value ?
            dnField.value.replace(/\./g, '\\.') + regexField.value :
            regexField.value;

        const status = validate_url_regexp(
            regexValue,
            excludeField.value,
            exampleField.value,
            loose);

        if (typeof status === 'undefined' || status instanceof RegExpStateSuccess) {
            regexField.setCustomValidity('');
            excludeField.setCustomValidity('');
            removeErrorClass(regexField, dnField, exampleField, excludeField);
        }
        else if(status instanceof ExcludeRegExpStateError) {
            excludeField.setCustomValidity(status.message);
            addErrorClass(regexField, dnField, exampleField, excludeField);
        }
        else if (status instanceof RegExpStateError) {
            regexField.setCustomValidity(status.message);
            addErrorClass(regexField, dnField, exampleField, excludeField);
        }

        return status;
    }

    function selectFields(child) {
        const parentSelector = '.field-matching_context';
        return listOfIdSelectors.map((_) => jQuery(child).parents(parentSelector).find(_)[0]);
    }

    function addErrorClass(regexField, dnField, exampleField, excludeField) {
        const parentSelector = '.field-matching_context .form-group';
        jQuery(regexField).parents(parentSelector).addClass('has-error');
        jQuery(dnField).parents(parentSelector).addClass('has-error');
        jQuery(exampleField).parents(parentSelector).addClass('has-warning');
        jQuery(excludeField).parents(parentSelector).addClass('has-error');
    }

    function removeErrorClass(regexField, dnField, exampleField, excludeField) {
        const parentSelector = '.field-matching_context .form-group';
        jQuery(regexField).parents(parentSelector).removeClass('has-error');
        jQuery(dnField).parents(parentSelector).removeClass('has-error');
        jQuery(exampleField).parents(parentSelector).removeClass('has-warning');
        jQuery(excludeField).parents(parentSelector).removeClass('has-error');
    }

    jQuery(($) => {
        const form = $('form[name$="notice"]');
        if (form.length > 0) {
            form.on('change', listOfIdSelectors.join(), (event) => validateFields(...selectFields(event.target)));
            form.on('submit', (event) => {
                const status = validateFields(...selectFields(listOfIdSelectors[0]));
                if (status instanceof RegExpStateError || status instanceof ExcludeRegExpStateError) {
                    event.preventDefault();
                }
            });
        }
    });
})();