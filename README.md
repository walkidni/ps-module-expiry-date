# Product Expiration Date Module

## Purpose

This repository contains the PrestaShop module:

- `psmoduleexpirydate`

It adds one nullable expiration date per product and exposes that data in:

- the Back Office product form
- the Back Office product list
- the Front Office product page

## Implemented behavior

### Storage

The module stores one nullable date per product in:

- `ps_psmoduleexpirydate`

Schema:

- `id_product` primary key
- `expiration_date` nullable `DATE`

### Back Office

The module adds:

- a `Date d'expiration` field in the product form
- a `Date d'expiration` column in the product list

Supported behavior:

- create/save
- update
- clear to `NULL`
- reload existing value in the form

### Front Office

On the product page:

- if a date exists, the module displays `Expire le : JJ/MM/AAAA`
- if no date exists, the module displays nothing

## Main hooks used

Back Office product form:

- `actionProductFormDataProviderData`
- `actionProductFormBuilderModifier`
- `actionAfterCreateProductFormHandler`
- `actionAfterUpdateProductFormHandler`

Back Office product list:

- `actionProductGridDefinitionModifier`
- `actionProductGridQueryBuilderModifier`

Front Office product page:

- `displayProductAdditionalInfo`

## Runtime integration

In the local Docker setup, this repository is mounted into PrestaShop at:

- `/var/www/html/modules/psmoduleexpirydate`

That technical module path matches the module name used by PrestaShop.


