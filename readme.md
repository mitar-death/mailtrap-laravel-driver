\# Mailtrap Laravel Driver

This package provides a Mailtrap driver for Laravel, allowing you to send emails in your Laravel application via the Mailtrap service.

\## Installation

You can install the package via composer:

\`\`\`bash  
composer require mazi/mailtrap-driver

## **Configuration**

After installing the package, you need to add the service provider to the `providers` array in `config/app.ph`

_\<?php_

_**'Mazi\\MailtrapDriver\\MailtrapServiceProvider'**_,

_?>_

Then, in your `.env` file, set the `MAIL_MAILER` or `MAIL_DRIVER` (depending on your Laravel version) to `mailtrap`:

MAIL_MAILER\_\=\_mailtrap

Also, add your Mailtrap API Token to your `.env` file:

MAILTRAP_API_TOKEN\_\=\_your-mailtrap-api-token

_\<?php_

_**Mail**::**to**_(_**'test@example.com'**_)_\->**send**_(_new_ _**TestMail**_());

_?>_
