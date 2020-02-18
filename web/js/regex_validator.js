(function () {
    const elements = (jQueryQuery) => Array.from(jQueryQuery);
    const single = (jQueryElements) => elements(jQueryElements)[0];

    const findForm = () => jQuery('form[name$="notice"]');
    const findExampleUrlField = () => jQuery(`input[id$="exampleUrl"]`);
    const findDomainsFields = () => jQuery(`select[id$="domainNames"],select[id$="domainsSets"]`);
    const findUrlRegexField = () => jQuery(`input[id$="urlRegex"]`);
    const findExcludeUrlRegexField = () => jQuery(`input[id$="excludeUrlRegex"]`);
    const findFields = () => [
        single(findExampleUrlField()),
        ...elements(findDomainsFields()),
        single(findUrlRegexField()),
        single(findExcludeUrlRegexField())
    ];
    const findFormGroupOfField = (field) => jQuery(field).parents('.field-matching_context .form-group');

    const markHasError = element => element.addClass('has-error');
    const markHasWarning = element => element.addClass('has-warning');
    const markIsClean = element => element.removeClass('has-warning').removeClass('has-error');

    const getSelectedDomainNames = () => Array.from(jQuery('select[id$="domainNames"] option:selected'))
        .map(option => jQuery(option).prop('label'))

    const getSelectedDomainSetsDomains = () => Array.from(jQuery('select[id$="domainsSets"] option:selected'))
        .map(option => jQuery(option).data('domains').split(','))
        .flat()

    const getAllRelatedDomains = () => [...new Set(
        getSelectedDomainNames().concat(getSelectedDomainSetsDomains())
    )]

    const injectRegexToDomain = baseRegex => domainName => domainName.replace(/\./g, '\\.') + baseRegex;

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

    function validateUrlRegexp(urlRegexps, exclude_url_regex, example_url, loose) {
        //Regex invalid
        let regexs, exclude_regex;
        try {
            regexs = urlRegexps.map(r => new RegExp(r, 'i'));
        } catch (e) {
            return new RegExpStateError('Cette regexp est invalide' + " (Détails: " + e.message + ")");
        }
        try {
            exclude_regex = exclude_url_regex && new RegExp(exclude_url_regex, 'i');
        } catch (e) {
            return new ExcludeRegExpStateError('Cette regexp est invalide' + " (Détails: " + e.message + ")");
        }

        //Regex matching way too many urls
        if (regexs[0].test('//google.com') && regexs[0].test('//lemonde.fr')) {
            return new RegExpStateError(catch_all_regex_msg + " : elle couvre //google.com et //lemonde.fr")
        }

        // Loose verification passed.
        if (loose === true) {
            return new RegExpStateSuccess();
        }

        // Regex matching too many urls
        const catch_all_regex_msg = 'Cette regexp est trop large';
        if (regexs[0].test('//google.com?foo=bar&bar=foo')) {
            return new RegExpStateError(catch_all_regex_msg + " : elle couvre //google.com?foo=bar&bar=foo");
        }
        if (regexs[0].test('//google.com/foo/bar')) {
            return new RegExpStateError(catch_all_regex_msg + " : elle couvre //google.com/foo/bar");
        }

        // Match url example
        if (example_url) {
            const matchingExample = regexs.find(r => r.test(example_url));
            if (!matchingExample)
                return new RegExpStateError('Cette regex ne marche pas avec l’exemple ' + example_url);
        }

        // Exclude url does not match url example
        if (example_url && exclude_regex && exclude_regex.test(example_url)) {
            return new ExcludeRegExpStateError('Cette regex d’exclusion ne devrait pas matcher l’exemple ' + example_url);
        }

        return new RegExpStateSuccess();
    }

    function validateFields() {
        markErrors();

        const exampleField = single(findExampleUrlField());
        const excludeField = single(findExcludeUrlRegexField());
        const regexField = single(findUrlRegexField());
        const loose = regexField.id.startsWith('restricted');

        const domainNames = getAllRelatedDomains();

        const status = regexField.value ? validateUrlRegexp(
            domainNames.length ? domainNames.map(injectRegexToDomain(regexField.value)) : [regexField.value],
            excludeField.value,
            exampleField.value,
            loose
        ) : undefined;

        if (typeof status === 'undefined' || status instanceof RegExpStateSuccess) {
            regexField.setCustomValidity('');
            excludeField.setCustomValidity('');
            cleanErrors();
        }
        else if(status instanceof ExcludeRegExpStateError) {
            excludeField.setCustomValidity(status.message);
            markErrors();
        }
        else if (status instanceof RegExpStateError) {
            regexField.setCustomValidity(status.message);
            markErrors();
        }

        return status;
    }

    function markErrors() {
        markHasWarning(findFormGroupOfField(findExampleUrlField()));
        elements(findDomainsFields()).forEach(field => markHasError(findFormGroupOfField(field)));
        markHasError(findFormGroupOfField(findUrlRegexField()));
        markHasError(findFormGroupOfField(findExcludeUrlRegexField()));
    }

    function cleanErrors() {
        findFields().forEach(field => markIsClean(findFormGroupOfField(field)));
    }

    jQuery(($) => {
        const form = findForm();
        if (form.length > 0) {
            form.change(() => validateFields());
            form.submit((event) => {
                const status = validateFields();
                if (status instanceof RegExpStateError || status instanceof ExcludeRegExpStateError) {
                    event.preventDefault();
                }
            });
        }
    });
})();
