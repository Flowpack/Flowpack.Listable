# Listable

This Neos package solves one problem: help you list any nodes in Fusion.
The idea is very simple: you often need to display list of things (e.g. news, articles etc), and the concern of listing items should better be separated from the concern of rendering items. This packages provides a solid foundation for listing, while allowing you to take care of rendering stuff on your own.

# TL;DR

1. Install the package with composer: `composer require flowpack/listable` [Here it is on packagist](https://packagist.org/packages/flowpack/listable).
2. Add `Flowpack.Listable:ListableMixin` to nodetypes that you want to list.
2. Build your list based on `Flowpack.Listable:Listable` for simple list or on `Flowpack.Listable:List` for list with a header and an archive link.
3. For each of your nodetypes create a new TS object of type NodeTypeName + 'Short', or manually definy a rendering object.
4. Rely on public API keys when overriding settings.

If you don't need the package, but love Fusion, [look here](https://github.com/Flowpack/Flowpack.Listable/blob/master/Resources/Private/Fusion/Api.ts2), you may find some inspiration :)

# Nodetype mixins

On data level we provide only one mixin: `Flowpack.Listable:ListableMixin`. The only thing you have to do, is to add this mixin to nodetypes that you would want to list with this package. That's right, planing all other fields is completely up to you.

# Fusion objects

Keys documented here are considered public API and would be treated with semantic versioning in mind. Extend all other properties at your own risk.

## Flowpack.Listable:Listable

At the heart of this package is a `Flowpack.Listable:Listable` object. It provides some good defaults for rendering lists of things, and is pretty extensible too. Here are Fusion context variables that you can configure for this object:

| Setting | Description | Defaults |
|---------|-------------|----------|
| listClass | Classname of UL tag | '' |
| itemClass | Classname of LI tag of wrapping each item | '' |
| sortProperty | Sort by property | '' |
| sortOrder | Sort order | 'DESC' |
| limit | Limit number of results. Set to high number when using with pagination | 10000 |
| offset | Offset results by some value (i.e. skip a number of first records) | 0 |
| paginationEnabled | Enable pagination | true |
| itemsPerPage | Number of items per page when using pagination | 24 |
| maximumNumberOfLinks | Number of page links in pagination | 15 |
| queryType | Predefined query types, choose between `getFromCurrentPage` and `getAll` | 'getAll' |
| itemRenderer | Object used for rendering child items. Within it you get two context vars set: `node` and `iterator` | 'Flowpack.Listable:ContentCaseShort' |

You may also override `collection` key with custom query. Sorting and pagination would still apply (via `@process`). Here's an example that lists first 10 objects of type `Something.Custom:Here`.

```
prototype(My.Custom:Object) < prototype(Flowpack.Listable:Listable) {
  @context.limit = 10
  collection = ${q(site).find('[instanceof Something.Custom:Here]').get()}
}
```

## Flowpack.Listable:List

There's often a need to render a list with a header an a archive link.
This object takes `Flowpack.Listable:Listable` and wraps it with just that.

This is just a helper object, and in many cases you would not want to use it,
but use `Flowpack.Listable:Listable` directly.

| Setting | Description | Defaults |
|---------|-------------|----------|
| wrapClass | Class of a div that wraps the whole object | '' |
| listTitle | Test of the title | '' |
| listTitleClass | Class of the list title | '' |
| archiveLink | Nodepath for the archive link | '' |
| archiveLinkTitle | Title of the archive link | '' |
| archiveLinkClass | Classname of the archive link | '' |
| archiveLinkAdditionalParams | AdditionalParams of the archive link, e.g. `@context.archiveLinkAdditionalParams = ${{archive: 1}}` | {} |

# Configuring pagination

Besides enabling pagination in TS object, you must not forget about adding pagination entryIdentifier to all parent cache objects, e.g. like this:

```
prototype(Neos.Neos:Page).@cache.entryIdentifier.pagination = ${request.pluginArguments.listable-paginate.currentPage}
root.@cache.entryIdentifier.pagination = ${request.pluginArguments.listable-paginate.currentPage}
```

To make urls pretty, see [Routes.yaml](https://github.com/flowpack/Flowpack.Listable/blob/master/Configuration/Routes.yaml).

# Helper FlowQueries you can use

## filterByDate

Filter nodes by properties of type date

## filterByReference

Filter nodes by properties of type reference or references

## sort

Sort nodes by any property.

Neos doesn't have built-in sort FlowQuery operation (originally due to performance considerations). Sorting large amounts of nodes in-memory won't perform well, and [a proper indexed search](https://github.com/Flowpack/Flowpack.ElasticSearch) would always do a better job. But if you have not too many nodes (~<10000) plain in-memory sorting may be a decent choice, especially for cached views. So give it a try before investing into more complex solutions.

Example:

    ${q(site).find('[instanceof Neos.Neos:Document]').sort('title', ['ASC'|'DESC']).get()}

## sortRecursiveByIndex

Sort nodes recursively by their sorting property.

Example:

    ${q(site).find('[instanceof Neos.Neos:Document]').sortRecursiveByIndex(['ASC'|'DESC']).get()}
