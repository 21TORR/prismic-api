Prismic Symfony Bundle
======================

Basic implementation bundle, that eases the integration of Prismic data into a Symfony application.

> 📚 [**Read the docs**](https://21torr-docs.fly.dev/docs/php/symfony/prismic/)


Installation
------------

Define the required `.env` variables:

```env
# The repository key
PRISMIC_REPOSITORY='...'

# The API token to fetch content from Prismic
PRISMIC_CONTENT_API_TOKEN='...'

# The API token to push content type changes to Prismic
PRISMIC_TYPES_API_TOKEN='...'

# An JSON array with the mapping of integration field key -> token
# example: '{"product_catalog": "abc", "test": "def"}'
PRISMIC_INTEGRATION_TOKENS='{...}'
```
