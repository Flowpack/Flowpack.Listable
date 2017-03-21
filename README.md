# Listable

This Neos package solves one problem: help you list any nodes in Fusion.
The idea is very simple: you often need to display list of things (e.g. news, articles etc), and the concern of listing items should better be separated from the concern of rendering items. This package provides a solid foundation for listing, while allowing you to take care of rendering stuff on your own.

# TL;DR

1. Install the package with composer: `composer require flowpack/listable` [Here it is on packagist](https://packagist.org/packages/flowpack/listable).
2. Add `Flowpack.Listable:ListableMixin` to nodetypes that you want to list.
2. Build your list based on `Flowpack.Listable:Listable` for a simple list or on `Flowpack.Listable:List` for a list with a header and an archive link.
3. For each of your nodetypes create a new Fusion object of type NodeTypeName + 'Short', or manually define a rendering object.
4. Rely on public API keys when overriding settings.

# Nodetype mixins

On data level we provide only one mixin: `Flowpack.Listable:ListableMixin`. The only thing you have to do, is to add this mixin to nodetypes that you would want to list with this package. That's right, planing all other fields is completely up to you.

# Fusion objects

Keys documented here are considered public API and would be treated with semantic versioning in mind. Extend all other properties at your own risk.

## Flowpack.Listable:Listable

At the heart of this package is the `Flowpack.Listable:Listable` object. It provides some good defaults for rendering lists of things, and is pretty extensible too. Here are the options that you can configure for this object:

| Setting | Description | Defaults |
|---------|-------------|----------|
| listClass | Classname of UL tag | '' |
| itemClass | Classname of LI tag wrapping each item | '' |
| sortProperty | Sort by property | '' |
| sortOrder | Sort order | 'DESC' |
| limit | Limit number of results. Set to high number when using with pagination | 10000 |
| offset | Offset results by some value (i.e. skip a number of first records) | 0 |
| paginationEnabled | Enable pagination | true |
| itemsPerPage | Number of items per page when using pagination | 24 |
| maximumNumberOfLinks | Number of page links in pagination | 15 |
| itemRenderer | Object used for rendering child items. Within it you get two context vars set: `node` and `iterator` | 'Flowpack.Listable:ContentCaseShort' |

You may also override `collection` key with custom query. Sorting and pagination would still apply (via `@process`). Here's an example that lists the first 10 objects of type `Something.Custom:Here`.

```
prototype(My.Custom:Object) < prototype(Flowpack.Listable:Listable) {
  limit = 10
  collection = ${q(site).find('[instanceof Something.Custom:Here]').get()}
}
```

## Flowpack.Listable:List

There's often a need to render a list with a header and an archive link.
This object takes `Flowpack.Listable:Listable` and wraps it with just that.

This is just a helper object, and in many cases you would not want to use it,
but use `Flowpack.Listable:Listable` directly.

| Setting | Description | Defaults |
|---------|-------------|----------|
| wrapClass | Class of the div that wraps the whole object | '' |
| listTitle | Title of the list | '' |
| listTitleClass | Class of the list title | '' |
| archiveLink | Nodepath for the archive link | '' |
| archiveLinkTitle | Title of the archive link | '' |
| archiveLinkClass | Classname of the archive link | '' |
| archiveLinkAdditionalParams | AdditionalParams of the archive link, e.g. `@context.archiveLinkAdditionalParams = ${{archive: 1}}` | {} |

## Flowpack.Listable:Pagination

TODO: describe how to use it. For now look at the source code. It's cool!

# Configuring pagination

Don't forget to configure @cache correctly for your listable objects. If you use pagination you may want to use `mode = 'dynamic'`.

# FlowQuery Helper you can use

## filterByDate

Filter nodes by properties of type date.

## filterByReference

Filter nodes by properties of type reference or references.

## sortRecursiveByIndex

Sort nodes recursively by their sorting property.

Example:

    ${q(site).find('[instanceof Neos.Neos:Document]').sortRecursiveByIndex('DESC').get()}
