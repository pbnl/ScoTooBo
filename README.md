# ScoTooBo

[![CircleCI](https://circleci.com/gh/pbnl/ScoTooBo.svg?style=svg)](https://circleci.com/gh/pbnl/ScoTooBo)
[![Code Climate](https://codeclimate.com/github/pbnl/ScoTooBo/badges/gpa.svg)](https://codeclimate.com/github/pbnl/ScoTooBo)
[![Test Coverage](https://codeclimate.com/github/pbnl/ScoTooBo/badges/coverage.svg)](https://codeclimate.com/github/pbnl/ScoTooBo/coverage)
[![Issue Count](https://codeclimate.com/github/pbnl/ScoTooBo/badges/issue_count.svg)](https://codeclimate.com/github/pbnl/ScoTooBo)

This is a toolbox for scouts to organize their work.

### Features
It works with a ldap and a mysql databse in the backgroung.
At the moment it provides following features:
* User management (show, add, del, modify)
* Event management
* Provide feedback
* Group management (show)

### Setup

To setup a working scotoobo version you can use the docker images.
Download the `docker-compose.yml` and run `docker-compose up` in the same directory.

You can edit the configuration by setting the environment variables in the `docker-compose.yml` file.
If you don't scotoobo will fallback to test related default values.


We used some nice libs for our work:
* Bootstrap https://getbootstrap.com/
* Symfony https://symfony.com/
* Tablesorter http://tablesorter.com/docs/
* Feedback.js https://experiments.hertzen.com/jsfeedback/
* Html2canvas https://html2canvas.hertzen.com/
* password-generator Bermi Ferrer <bermi@bermilabs.com>
* Imageviewer https://fengyuanchen.github.io/viewerjs/
* Re-Captcha https://www.google.com/recaptcha/intro/android.html
Thanks for their work!
