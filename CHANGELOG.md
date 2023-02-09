# Changelog

All notable changes to `ai-email-suggest` will be documented in this file.

### 1.0.4 Added email validation

## Tests refactoring - 2023-02-09

Refactored tests

## Optimised cache management - 2023-02-09

Cache will store only domains (i.e.: "yaohh.com") instead of full email addresses, to limit even more the number of call to OpenAI.
