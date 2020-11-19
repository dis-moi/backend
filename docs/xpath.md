XPath
=====
You can use XPath 1.0 expressions in a `MatchingContext`.

> *__XPath__ (XML Path Language) is a query language for selecting nodes from an XML document.*
> 
> https://fr.wikipedia.org/wiki/XPath

> :warning: __You'll need a basic understanding of the HTML language before reading further:__
> - https://www.codecademy.com/catalog/language/html-css
> - https://www.sololearn.com/Course/HTML/
> - :fr: https://openclassrooms.com/fr/courses/1730206-apprenez-asp-net-mvc/1808781-introduction-au-html

XPath also works on HTML document, because an Html document is very closely related to an XML document:
> *XML mainly focuses on transfer of data while HTML is focused on presentation of the data.*
> 
> https://www.guru99.com/xml-vs-html-difference.html


Matching text in page
---------------------
The principal use case in the context of __Dismoi__ is to match a text, or part of a text, on a page.
> This is great power but never forget: 
> *"With great power comes great responsibility."*

There are already a lot's of great Cheat Sheet out there, but here's a sample on __Text & Link__:
```
'//*[.="t"]'                  // element containing text 't' exactly
'//E[contains(text(), "t")]'  // element <E> containing text 't' (css: E:contains('t'))
'//a'                         // link element (css: a)
'//a[.="t"]'                  // element <a> containing text 't' exactly
'//a[contains(text(), "t")]'  // element <a> containing text 't' (css: a:contains('t'))
'//a[@href="url"]'            // <a> with target link 'url' (css: a[href='url'])
'//a[.="t"]/@href'            // link URL labeled with text 't' exactly
```
> See the complete list at https://gist.github.com/LeCoupa/8c305ec8c713aad07b14

### Examples

1. Matching every page on **LeBonCoin** containing "Western Union":
- __Domaines__ `leboncoin.fr`
- __Regex recherchée *__ `.*`
- __Regex d'exclusion__ 
- __XPath recherché__ `/html/body//text()[contains(.,'Western Union')]`

Here you could also want to match any case variations of the text, for example:
- test
- TEST
- Test

It would go like this in XPath 1.0:
```
/html/body//text()[contains(translate(., 'TES', 'tes'), 'test')]
```
> All letters presents in the `test` word must appears in __uppercase__ and __lowercase__.

> By using version 2.0 of the XPath syntax we could run the following expresion instead:
> `/html/body//text()[matches(.,'western union', 'i')]`.
> > The `matches` keyword allow the use of *Regular Expressions* instead of simple text.
> > Here the `i` stands for __case `i`nsensitive__ (:fr: *insensible à la casse*).

You could also match text present in the whole HTML document `//text()[contains(.,'Western Union')]` 
and thus also text that may be presents in the `<head>` of the document.

> But please, be sensible, if you don't __need__ other parts of the document, be specific.
> At least use `/html/body/` prefix to only match text in the `<body>` of the page.

Playground
----------
While there are a few playgrounds (tester/sandbox) out there :
- https://www.freeformatter.com/xpath-tester.html
- http://www.xpathtester.com/xpath

They might not work for all you webpages HTML.

### Console
Here you're best friend will be your browser's console:

1. Open the console by pressing `Ctrl+Maj+i` (it should works on __Chrom*__ and __Firefox__).
2. Enter the following command in the console:
```
document.evaluate("/html/body/text()[contains(.,'scam')]", document, null, XPathResult.BOOLEAN_TYPE, null);
```
> `XPathResult {resultType: 3, booleanValue: true, invalidIteratorState: false}`

Here for example if you see `booleanValue: true` it means that the string `scam` was found on the page.
> This is *more or less* the same code run by the extension.

#### Advanced usage

1. Open the console.
2. Enter **only once** the following command in console:
```
const xpath = expression => {
  const { booleanValue } = document.evaluate(expression, document, null, XPathResult.BOOLEAN_TYPE, null);
  return booleanValue;
}
```
3. For all your subsequent tests you can now run this simpler expression instead:
```
xpath("/html/body/text()[contains(.,'scam')]");
```
> If you open a new page remember to run through step __1.__ and __2.__ again.

Resources
---------
- https://www.codecademy.com/catalog/language/html-css
- https://www.sololearn.com/Course/HTML/
- :fr: https://openclassrooms.com/fr/courses/1730206-apprenez-asp-net-mvc/1808781-introduction-au-html
- https://www.guru99.com/xml-vs-html-difference.html
- https://fr.wikipedia.org/wiki/XPath
- :fr: https://www.ionos.fr/digitalguide/sites-internet/developpement-web/tutoriel-xpath/
- https://gist.github.com/LeCoupa/8c305ec8c713aad07b14
- https://lzone.de/cheat-sheet/XPath