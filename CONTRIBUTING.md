# Contributing to DisMoi backend

:+1::tada: First off, thanks for taking the time to contribute! :tada::+1:


This is a pretty basic Symfony/EasyAdmin application

The following is a set of guidelines for contributing to DisMoi. 
These are mostly guidelines, not rules. Use your best judgment, and feel free to propose changes to this document in a pull request.


## Code of conduct

This project and everyone participating in it is governed by the [DisMoi Code of Conduct](CODE_OF_CONDUCT.md).
By participating, you are expected to uphold this code.

## Lang

Most members of the team do speak :fr: **French** but not all as their mother tongue.
Since the team is international, we use :gb: **English** as the default lang in code, docs, issues, etc.

## How Can I Contribute?

### Reporting Bugs

This section guides you through submitting a bug report. Following these guidelines helps maintainers and the community understand your report :pencil:, reproduce the behavior :computer: :computer:, and find related reports :mag_right:.

#### How Do I Submit A (Good) Bug Report?

Explain the problem and include additional details to help maintainers reproduce the problem:

* **Use a clear and descriptive title** for the issue to identify the problem.
* **Describe the exact steps which reproduce the problem** in as many details as possible. 
* **Provide specific examples to demonstrate the steps**. 

Provide more context by answering these questions:

### Suggesting Enhancements

#### How Do I Submit A (Good) Enhancement Suggestion?


* **Use a clear and descriptive title** for the issue to identify the suggestion.
* **Provide a step-by-step description of the suggested enhancement** in as many details as possible.
* **Provide specific examples to demonstrate the steps**.
* **Describe the current behavior** and **explain which behavior you expected to see instead** and why.
* **Explain why this enhancement would be useful** to most users.

### Your First Code Contribution

Unsure where to begin contributing ? You can start by looking through these [Help wanted issues][help-wanted] issues.

### Local development

#### Environment Variables

You can edit .env to your liking (optional). This file should be .gitignored some time. 

#### Dev (w/ docker)

##### Install and run docker

[https://docs.docker.com/get-docker/](https://docs.docker.com/get-docker/)

The first run may take few minutes since images are to be built,
composer has to install project dependencies and doctrine has to
migrate databases and load fixtures. Subsequent runs are much faster.

```shell script
$ docker-compose up
```

A [Mailhog](https://github.com/mailhog/MailHog) service is available for email testing in `dev` environment at:
[http://localhost:8025/](http://localhost:8025/)

#### Aliases

Some aliases are conveniently made available... 

```shell script
$ . ./alias
$ aphp composer install
$ aphp bin/console assets:install web
$ dMigrate && dLoad
```

#### Tests

Setup and migrate test database and run phpunit:

```shell script
$ runtests
```

#### Admin access
[http://localhost:8088](http://localhost:8088)


### Pull Requests

The process described here has several goals:

- Improve or at least maintain the backend's quality
- Fix problems that are important to users
- Enable a sustainable system for maintainers to review contributions

After you submit your pull request, verify that all [status checks](https://help.github.com/articles/about-status-checks/) are passing <details><summary>What if the status checks are failing?</summary>If a status check is failing, and you believe that the failure is unrelated to your change, please leave a comment on the pull request explaining why you believe the failure is unrelated. A maintainer will re-run the status check for you. If we conclude that the failure was a false positive, then we will open an issue to track that problem with our status check suite.</details>

While the prerequisites above must be satisfied prior to having your pull request reviewed, the reviewer(s) may ask you to complete additional design work, tests, or other changes before your pull request can be ultimately accepted.

## Styleguides

### PHP Styleguide

All PHP code must adhere to [PHP The Right Way](https://phptherightway.com/#code_style_guide) code style.

We also use [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to enforce @Symfony code style.

### Git Commit Messages

We use [conventional commit](https://www.conventionalcommits.org/en/v1.0.0/) messages guidelines

Each commit message consists of a **header**, a **body** and a **footer**.  The header has a special
format that includes a **type**, a **scope** and a **subject**:

```
<type>(<scope>): <subject>
<BLANK LINE>
<body>
<BLANK LINE>
<footer>
```

The **header** is mandatory and the **scope** of the header is optional.

Any line of the commit message cannot be longer than 100 characters! This allows the message to be easier
to read on GitHub as well as in various git tools.

The footer should contain a [closing reference to an issue](https://help.github.com/articles/closing-issues-via-commit-messages/) if any.

Samples: 

```
docs(readme): update README to open source code
```
```
fix(contributors): contributors are not sorted

Contributors are now sorted in alphabetical order.
```

#### Type
Must be one of the following:

* **build**: Changes that affect the build system or external dependencies
* **ci**: Changes to our CI configuration files and scripts
* **docs**: Documentation only changes
* **feat**: A new feature or functional change
* **fix**: A bug fix
* **perf**: A code change that improves performance
* **refactor**: A code change that neither fixes a bug nor adds a feature
* **style**: Changes that do not affect the meaning of the code (white-space, formatting, missing type, etc)
* **test**: Adding missing tests or correcting existing tests

#### Subject
The subject contains a succinct description of the change:

* use the imperative, present tense: "change" not "changed" nor "changes"
* don't capitalize the first letter
* no dot (.) at the end

#### Body
Just as in the **subject**, use the imperative, present tense: "change" not "changed" nor "changes".
The body should include the motivation for the change and contrast this with previous behavior.

#### Footer
The footer should contain any information about **Breaking Changes** and is also the place to
reference GitHub issues that this commit **Closes**.

**Breaking Changes** should start with the word `BREAKING CHANGE:` with a space or two newlines. The rest of the commit message is then used for this.

## Deployment

Here’s some information on how to deploy to [CleverCloud](https://www.clever-cloud.com):

[Using CleverCloud](docs/using_clever_cloud.md) 

--------------------------------------------

Thanks! :heart: :heart: :heart:

DisMoi Team
