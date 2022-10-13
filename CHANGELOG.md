6.3.3
=====

* (improvement) Use wired `HttpClient` to ease debugging.


6.3.2
=====

* (improvement) Use PHPUnit 9.5.
* (bug) Pass transformed value into visitor in `EmbedField`.
* (bug) Allow empty `EmbedField`s.
* (improvement) Add tests for `EmbedField` validation.


6.3.1
=====

* (bug) Add early exit on `validateData` of `GroupField` if `$data` is not an array.


6.3.0
=====

* (feature) Add URL rewriter to rewrite static URLs.


6.2.1
=====

* (improvement) Don't cache the environment.


6.2.0
=====

* (improvement) Clear stateful services on worker start.
* (feature) Provide access to alternate language ids of documents.


6.1.2
=====

* (improvement) Allow empty target locales for document links.


6.1.1
=====

* (improvement) Also call data visitor for null fields.
* (improvement) Clean up code in slice data transformation.


6.1.0
=====

* (feature) Add getter for slug + all outdated slugs in documents.


6.0.1
=====

* (bug) Handle broken links in the data transformer.


6.0.0
=====

* (improvement) Overhaul translation check visitor.
* (bc) Changed interface of translation check visitor.


5.1.0
=====

* (improvement) Add timeout for API requests.
* (feature) Add `DataVisitorInterface` to inspect every dataset.
* (feature) Add `TranslationCheckVisitor`.
* (feature) Add `PrismicBackendUrlGenerator` to generate URLs to the prismic backend.


5.0.2
=====

* (improvement) Import integration field entries in batches.


5.0.1
=====

* (improvement) Add proper validation for `ColorField`s.
* (improvement) Make return types in transform calls narrower.


5.0.0
=====

* (improvement) Add logging for the prismic integration field API client.
* (improvement) Require PHP 8.1+
* (bc) Completely refactor the validation structure.
* (bc) The class `FieldValueTransformer` was renamed to `DataTransformer`.
* (bug) Remove useless validation of `UidField`s.
* (feature) Properly resolve document links in RTE fields.


4.0.0
=====

*   (improvement) Allow Symfony v6.


4.0.0-beta.10
=============

*   (feature) Add `PrismicApi::search()` as a base search helper.
*   (bug) Fix passing multiple predicates to the Prismic API.


4.0.0-beta.9
============

*   (feature) Added `SliceExtraDataGenerator` to be able to append extra data to slices.


4.0.0-beta.8
============

*   (bug) Explicitly pass language `*` is none is given.


4.0.0-beta.7
============

*   (improvement) Add support to load unpublished documents.


4.0.0-beta.6
============

*   (improvement) Add missing value transformation in `GroupField`.
*   (improvement) Add helper to transform a single field in `Document::transformField()`.


4.0.0-beta.5
============

*   (improvement) Build `catalog` value on `IntegrationField`.
*   (bug) Add check if property `kind` in `LinkField` is `image` to return `ImageValue`.


4.0.0-beta.4
============

*   (feature) Add `PrismicIntegrationFieldApi`.


4.0.0-beta.3
============

*   (bug) Add empty extra field for slices.
*   (feature) Add `ImageValue` and `VideoValue`.
*   (feature) Implement `EmbedField` (it only supports videos for now).
*   (bug) Fix invalid initialization of `SliceValidationCompound`.
*   (feature) Add `DocumentLinkValue` and use it in `LinkField`.


4.0.0-beta.2
============

*   (bug) Fix invalid index map key.


4.0.0-beta.1
============

*   (bc) Completely refactor the whole implementation.


3.0.0
=====

*   (improvement) Language is optional again in `PrismicApi::searchDocuments()`.
*   (bc) Change order of parameters in `PrismicApi::searchDocuments()`.


2.0.1
=====

*   (improvement) Add languages getter for `Environment`.


2.0.0
=====

*   (feature) Add language support for the API client.
*   (bc) Add language as required parameter to `PrismicApi::searchDocuments()`.


1.0.5
=====

*   (improvement) Extract `filterOptionalFields` to `FilterFieldsHelper`.
*   (improvement) Filter empty arrays in `filterOptionalFields` out.
*   (bug) Add missing filter for optional fields in `Slice`.
*   (bug) Fix wrong value of `[config][labels]` in `SliceZone`.


1.0.4
=====

*   (improvement) Make custom type `Slice` easier to use.


1.0.3
=====

*   (bug) Add missing filter for optional fields in `SelectField`.
*   (improvement) Make `Slice` extendable.


1.0.2
=====

*   (bug) Fix invalid link type key.
*   (bug) Fix invalid check on whether the custom type already exists.
*   (improvement) Mark `PrismicTypeInterface` as internal.


1.0.1
=====

*   (bug) Properly pass rich text config.


1.0.0
=====

*   (feature) Added new implementation.
*   (feature) Added migrations.
*   (feature) Added input fields.


0.1.1
=====

*   (bug) `isMasterRef` is not set, if it isn't `true`.


0.1.0
=====

*   (improvement) Basic first implementation.
