(function () {
    const matchingContextFieldSelector = ' .field-matching_context ';
    const formGroupSelector = ' .form-group ';
    const excludeUrlRegexFieldSelector = 'textarea#notice_excludeUrlRegex';

    const elements = (jQueryQuery) => Array.from(jQueryQuery);
    const single = (jQueryElements) => elements(jQueryElements)[0];

    const findForm = () => jQuery('form[name$="notice"]');

    const findMatchingContextGroups = () => jQuery(matchingContextFieldSelector);

    const findExampleUrlField = (matchingContextField) => jQuery(matchingContextField)
      .find(`input[id$="exampleUrl"]`);

    const findDomainsFields = (matchingContextField) => jQuery(matchingContextField)
      .find(`select[id$="domainNames"],select[id$="domainsSets"]`);

    const findUrlRegexField = (matchingContextField) => jQuery(matchingContextField)
      .find(`textarea[id$="urlRegex"]`);

    const findMatchingContextExcludeUrlRegexField = (matchingContextField) => jQuery(matchingContextField)
      .find(`textarea[id$="excludeUrlRegex"]`);

    const findNoticeExcludeUrlRegexField = () => jQuery(excludeUrlRegexFieldSelector);

    const findFields = (matchingContextField) => [
        single(findExampleUrlField(matchingContextField)),
        ...elements(findDomainsFields(matchingContextField)),
        single(findUrlRegexField(matchingContextField)),
        single(findMatchingContextExcludeUrlRegexField(matchingContextField))
    ];

    const findFormGroupOfField = (field) => jQuery(field).closest(formGroupSelector);

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
        .flat();

    const getAllRelatedDomains = (matchingContextField) => [...new Set(
        getSelectedDomainNames(matchingContextField).concat(getSelectedDomainSetsDomains(matchingContextField))
    )];

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
    class ValidationError extends RegExpState {
        static get success() {
            return false;
        }
    }
    class RegExpStateError extends ValidationError {}
    class MatchingContextExcludeRegExpStateError extends ValidationError {}
    class NoticeExcludeRegExpStateError extends ValidationError {}

    function validateUrlRegexp(urlRegexps, matchingContextExcludeRegex, noticeExcludeRegex, example_url, loose) {
        //Regex invalid
        let regexs, MatchingContextExcludeRegex, NoticeExcludeRegex;
        try {
            regexs = urlRegexps.map(r => new RegExp(r, 'i'));
        } catch (e) {
            return new RegExpStateError('Cette regexp est invalide' + " (Détails: " + e.message + ")");
        }

        // Invalid matching context exclude regex
        try {
            MatchingContextExcludeRegex = matchingContextExcludeRegex && new RegExp(matchingContextExcludeRegex, 'i');
        } catch (e) {
            return new MatchingContextExcludeRegExpStateError('Cette regexp est invalide' + " (Détails: " + e.message + ")");
        }

        // Invalid notice exclude regex
        try {
            NoticeExcludeRegex = noticeExcludeRegex && new RegExp(noticeExcludeRegex, 'i');
        } catch (e) {
            return new NoticeExcludeRegExpStateError('Cette regexp est invalide' + " (Détails: " + e.message + ")");
        }

        const catch_all_regex_msg = 'Cette regexp est trop large';

        //Regex matching way too many urls
        if (regexs[0].test('//google.com') && regexs[0].test('//lemonde.fr')) {
            return new RegExpStateError(catch_all_regex_msg + " : elle couvre //google.com et //lemonde.fr")
        }

        // Loose verification passed.
        if (loose === true) {
            return new RegExpStateSuccess();
        }

        // Regex matching too many urls
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

        // MC Exclude url does not match url example
        if (example_url && MatchingContextExcludeRegex && MatchingContextExcludeRegex.test(example_url)) {
            return new MatchingContextExcludeRegExpStateError('Cette regex d’exclusion ne devrait pas matcher l’exemple ' + example_url);
        }

        // Notice Exclude url does not match url example
        if (example_url && NoticeExcludeRegex && NoticeExcludeRegex.test(example_url)) {
            return new NoticeExcludeRegExpStateError('Cette regex d’exclusion ne devrait pas matcher l’exemple ' + example_url);
        }

        return new RegExpStateSuccess();
    }

    function validateAllMatchingContexts() {
        const errors = findMatchingContextGroups()
            .map((index, mc) => validateMatchingContext(mc))
            .filter(status => status instanceof ValidationError);

        return errors && errors[0]
    }

    function validateMatchingContext(matchingContextField) {
        markErrors(matchingContextField);

        const exampleField = single(findExampleUrlField(matchingContextField));
        const matchingContextExcludeField = single(findMatchingContextExcludeUrlRegexField(matchingContextField));
        const noticeExcludeField = single(findNoticeExcludeUrlRegexField());
        const regexField = single(findUrlRegexField(matchingContextField));
        const loose = regexField.id.startsWith('restricted');

        const domainNames = getAllRelatedDomains(matchingContextField);

        const status = regexField.value ? validateUrlRegexp(
            domainNames.length ? domainNames.map(injectRegexToDomain(regexField.value)) : [regexField.value],
            matchingContextExcludeField.value,
            noticeExcludeField.value,
            exampleField.value,
            loose
        ) : undefined;

        regexField.setCustomValidity('');
        matchingContextExcludeField.setCustomValidity('');
        noticeExcludeField.setCustomValidity('');

        if (typeof status === 'undefined' || status instanceof RegExpStateSuccess) {
            cleanErrors(matchingContextField);
        }
        else if (status instanceof RegExpStateError) {
            regexField.setCustomValidity(status.message);
            markErrors(matchingContextField);
        }
        else if(status instanceof MatchingContextExcludeRegExpStateError) {
            matchingContextExcludeField.setCustomValidity(status.message);
            markErrors(matchingContextField);
        }
        else if(status instanceof NoticeExcludeRegExpStateError) {
            noticeExcludeField.setCustomValidity(status.message);
            markErrors(matchingContextField);
        }

        return status;
    }

    function markErrors(matchingContextField) {
        markHasWarning(findFormGroupOfField(findExampleUrlField(matchingContextField)));
        elements(findDomainsFields(matchingContextField)).forEach(field => markHasError(findFormGroupOfField(field)));
        markHasError(findFormGroupOfField(findUrlRegexField(matchingContextField)));
        markHasError(findFormGroupOfField(findMatchingContextExcludeUrlRegexField(matchingContextField)));
        markHasError(findFormGroupOfField(findMatchingContextExcludeUrlRegexField(matchingContextField)));
        markHasError(findFormGroupOfField(findNoticeExcludeUrlRegexField()));
    }

    function cleanErrors(matchingContextField) {
        findFields(matchingContextField).forEach(field => markIsClean(findFormGroupOfField(field)));
        markIsClean(findFormGroupOfField(findNoticeExcludeUrlRegexField()));
    }

    jQuery(($) => {
        const form = findForm();
        if (form.length > 0) {
            form.on(
                'change keyup',
                matchingContextFieldSelector,
                function (event) { validateMatchingContext(this); }
            );
            findNoticeExcludeUrlRegexField().on('change keyup', validateAllMatchingContexts);
            form.on(
                'submit',
                function (event) {
                    try {
                        const status = validateAllMatchingContexts();
                        if (status instanceof ValidationError) {
                            event.preventDefault();
                        }
                    } catch (e) {
                        event.preventDefault();
                    }
                }
            );
        }
    });
})();
