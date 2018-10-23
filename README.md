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

After you started the app for the first time go into the container by executing: `sudo docker exec -it pbnldocker_php_1 bash`.
Replace `pbnldocker_php_1` with the name of the container. Then go into the app directory and execute
`php bin/console doctrine:schema:update --force` to create/update the sql database.

Noe you have to clear, warm up the cache and create the assets. Execute 
`export SYMFONY_ENV=prod && export APP_ENV=prod && composer run-script post-install-cmd --no-interaction --no-dev`
in the same directory to do this for an productive environment.

The system requires some groups in the ldap:
`buvo`, `elder`, `nordlichter`, `stavo`, `wiki`
The stamm of an user is detected by the ou (of the ldap) he was placed in. So make sure that 
this folders are spelt correctly. 

Thats it. Now you have a working scotoobo installation.


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
