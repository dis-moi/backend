(function () {
    const matchingContextFieldSelector = ' .field-matching_context ';
    const formGroupSelector = ' .form-group ';

    const elements = (jQueryQuery) => Array.from(jQueryQuery);
    const single = (jQueryElements) => elements(jQueryElements)[0];

    const findForm = () => jQuery('form[name$="notice"]');

    const findExampleUrlField = (matchingContextField) => jQuery(matchingContextField)
      .find(`input[id$="exampleUrl"]`);

    const findDomainsFields = (matchingContextField) => jQuery(matchingContextField)
      .find(`select[id$="domainNames"],select[id$="domainsSets"]`);

    const findUrlRegexField = (matchingContextField) => jQuery(matchingContextField)
      .find(`input[id$="urlRegex"]`);

    const findExcludeUrlRegexField = (matchingContextField) => jQuery(matchingContextField)
      .find(`input[id$="excludeUrlRegex"]`);

    const findFields = (matchingContextField) => [
        single(findExampleUrlField(matchingContextField)),
        ...elements(findDomainsFields(matchingContextField)),
        single(findUrlRegexField(matchingContextField)),
        single(findExcludeUrlRegexField(matchingContextField))
    ];

    const findFormGroupOfField = (field) => jQuery(field).parents(matchingContextFieldSelector+formGroupSelector);

    const markHasError = element => element.addClass('has-error');
    const markHasWarning = element => element.addClass('has-warning');
    const markIsClean = element => element.removeClass('has-warning').removeClass('has-error');

    const getSelectedDomainNames = (matchingContextField) => Array.from(
      jQuery(matchingContextField).find('select[id$="domainNames"] option:selected')
    ).map(option => jQuery(option).prop('label'))

    const getSelectedDomainSetsDomains = (matchingContextField) => Array.from(
      jQuery(matchingContextField).find('select[id$="domainsSets"] option:selected')
    )
        .map(option => jQuery(option).data('domains').split(','))
        .flat()

    const getAllRelatedDomains = (matchingContextField) => [...new Set(
        getSelectedDomainNames(matchingContextField).concat(getSelectedDomainSetsDomains(matchingContextField))
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
    class ExcludeRegExpStateError extends RegExpState {
        static get success() {
            return false;
        }
    }

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

    function validateFields(matchingContextField) {
        markErrors(matchingContextField);

        const exampleField = single(findExampleUrlField(matchingContextField));
        const excludeField = single(findExcludeUrlRegexField(matchingContextField));
        const regexField = single(findUrlRegexField(matchingContextField));
        const loose = regexField.id.startsWith('restricted');

        const domainNames = getAllRelatedDomains(matchingContextField);

        const status = regexField.value ? validateUrlRegexp(
            domainNames.length ? domainNames.map(injectRegexToDomain(regexField.value)) : [regexField.value],
            excludeField.value,
            exampleField.value,
            loose
        ) : undefined;

        if (typeof status === 'undefined' || status instanceof RegExpStateSuccess) {
            regexField.setCustomValidity('');
            excludeField.setCustomValidity('');
            cleanErrors(matchingContextField);
        }
        else if (status instanceof RegExpStateError) {
            regexField.setCustomValidity(status.message);
            excludeField.setCustomValidity('');
            markErrors(matchingContextField);
        }
        else if(status instanceof ExcludeRegExpStateError) {
            regexField.setCustomValidity('');
            excludeField.setCustomValidity(status.message);
            markErrors(matchingContextField);
        }

        return status;
    }

    function markErrors(matchingContextField) {
        markHasWarning(findFormGroupOfField(findExampleUrlField(matchingContextField)));
        elements(findDomainsFields(matchingContextField)).forEach(field => markHasError(findFormGroupOfField(field)));
        markHasError(findFormGroupOfField(findUrlRegexField(matchingContextField)));
        markHasError(findFormGroupOfField(findExcludeUrlRegexField(matchingContextField)));
    }

    function cleanErrors(matchingContextField) {
        findFields(matchingContextField).forEach(field => markIsClean(findFormGroupOfField(field)));
    }

    jQuery(($) => {
        const form = findForm();
        if (form.length > 0) {
            form.on(
                'change keyup',
                matchingContextFieldSelector,
                function (event) { validateFields(this); }
            );
            form.on(
                'submit',
                matchingContextFieldSelector,
                function (event) {
                    const status = validateFields(this);
                    if (status instanceof RegExpStateError || status instanceof ExcludeRegExpStateError) {
                        event.preventDefault();
                    }
                }
            );
        }
    });
})();
