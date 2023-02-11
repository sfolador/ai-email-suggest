# Changelog

All notable changes to `ai-email-suggest` will be documented in this file.

### 1.0.4 Added email validation

## Laravel 10 support - 2023-02-11

Added Pint 2.0 and support for Laravel 10

## Throttle requests and clear cache - 2023-02-11

- Added a throttle feature to allow users to throttle requests to the ai-email-suggest endpoint to prevent too many api calls.
- added a command to clear the email suggestion cache (only for cache drivers that support tagging)

## Fixes - 2023-02-09

fixed wrong api key in provider

## Tests refactoring - 2023-02-09

Refactored tests

## Optimised cache management - 2023-02-09

Cache will store only domains (i.e.: "yaohh.com") instead of full email addresses, to limit even more the number of call to OpenAI.
