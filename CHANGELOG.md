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
