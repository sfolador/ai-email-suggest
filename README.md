# Never worry about typos in email addresses

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sfolador/ai-email-suggest.svg?style=flat-square)](https://packagist.org/packages/sfolador/ai-email-suggest)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sfolador/ai-email-suggest/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sfolador/ai-email-suggest/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sfolador/ai-email-suggest/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sfolador/ai-email-suggest/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sfolador/ai-email-suggest.svg?style=flat-square)](https://packagist.org/packages/sfolador/ai-email-suggest)

This package provides a simple way to suggest email addresses based on a given email address. The typical use case is
when a user enters an email address with typos, i.e.: example@gmial.com. 
This package uses OpenAI to suggest an email addresses that are likely to be correct.

## Installation

You can install the package via composer:

```bash
composer require sfolador/ai-email-suggest
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="ai-email-suggest-config"
```

This is the contents of the published config file:

```php
return [
    'model' => 'text-davinci-003',
    'openai_key' => env('OPENAI_KEY'),
    'default_response' => 'Maybe you meant %suggestion%?'
];
```

*Remember to set your OpenAI key in your .env file.*

## Usage

```php
$aiEmailSuggest = AiEmailSuggest::suggest('test@yaohh.com');
// $aiEmailSuggest = 'test@yahoo.com'
```

It's possible to use this package from the frontend by invoking the route: "/ai-email-suggest" in POST by passing the email as a parameter.
The response will be a JSON with the suggested email, such as:

```json
{
  "suggestion": "test@yahoo.com"
}
```



## Testing

```bash
composer test
```
## TODO

- [ ] Add caching to avoid too many requests to OpenAI
- [ ] Add throttling to avoid too many requests to OpenAI


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [sfolador](https://github.com/sfolador)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
