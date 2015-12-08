# Listable

For the neos user interface, this package provides a content element "List" (experimental) that can render a list of nodes with a ListableMixin that can be added to node types that you want to list.

As a website creator, you usually simply render lists with one Fluid template that has a ``for`` statement that iterates over a list item collection you passed to it (best MVC practice). https://neos.readthedocs.org/en/stable/CreatingASite/RenderingCustomMarkup/Templating.html

If using this package from typoscript, you have to pass a collection to a typoscript object instead, togher with template generation parameters that this package provides, and it returns the rendered html based on its default template plus your custom ``*Short`` item template. Thus, instead of using a typoscript controller only to assemble the list object to be rendered and leaving the rendering enirely to the Fluid templating features, you now define rendering features (like classes etc.) as parameters of this package's typoscript objects.


The idea is very simple: you often need to display list of things (e.g. news, articles etc), and the concern of listing items should better be separated from the concern of rendering items. This packages provides a solid foundation for listing, while allowing you to take care of rendering stuff on your own.

# TL;DR

1. Install the package with composer. [Here it is on packagist](https://packagist.org/packages/sfi/listable).
2. Add `Sfi.Listable:ListableMixin` to nodetypes that you want to list.
2. Build your list based on `Sfi.Listable:Listable` for simple list or on `Sfi.Listable:List` for list with a header and an archive link.
3. For each of your nodetypes create a new TS object of type NodeTypeName + 'Short', or manually definy a rendering object.
4. Rely on public API keys when overriding settings.

[See here](https://github.com/sfi-ru/KateheoDistr/blob/master/Packages/Sites/Sfi.Kateheo/Resources/Private/TypoScript/NodeTypes/PageMain.ts2#L5) for a trivial integration example.

If you don't need the package, but want to see how list generation is done in TypoScript, [look here](https://github.com/sfi-ru/Sfi.Listable/blob/master/Resources/Private/TypoScript/Api.ts2), you may find some inspiration :)

# Nodetype mixins

On data level we provide only one mixin: `Sfi.Listable:ListableMixin`. The only thing you have to do, is to add this mixin to nodetypes that you would want to list with this package. That's right, planing all other fields is completely up to you.

# TypoScript objects

Keys documented here are considered public API and would be treated with semantic versioning in mind. Extend all other properties at your own risk.

## Sfi.Listable:Listable

At the heart of this package is a `Sfi.Listable:Listable` object. It provides some basic means for rendering lists of things (like quering, sorting, paginating etc), and is pretty extensible too. Here are TypoScript context variables that you can configure for this object:

| Setting | Description | Defaults |
|---------|-------------|----------|
| listClass | Classname of UL tag | '' |
| itemClass | Classname of LI tag of wrapping each item | '' |
| sortProperty | Sort by property | 'date' |
| sortOrder | Sort order | 'DESC' |
| limit | Limit number of results. Set to high number when using with pagination | 10000 |
| offset | Offset results by some value (i.e. skip a number of first records) | 0 |
| paginationEnabled | Enable pagination | true |
| itemsPerPage | Number of items per page when using pagination | 24 |
| maximumNumberOfLinks | Number of page links in pagination | 15 |
| queryType | Predefined query types, choose between `getFromCurrentPage` and `getAll` | 'getAll' |
| itemRenderer | Object used for rendering child items | 'Sfi.Listable:ContentCaseShort' |

You may also override `collection` key with custom query. Sorting and pagination would still apply (via `@process`). Here's an example that lists first 10 objects of type `Something.Custom:Here`.

```
prototype(My.Custom:Object) < prototype(Sfi.Listable:Listable) {
  @context.limit = 10
  collection = ${q(site).find('[instanceof Something.Custom:Here]').get()}}
}
```

## Sfi.Listable:List

There's often a need to render a list with a header an a archive link.
This object takes `Sfi.Listable:Listable` and wraps it with just that.

This is just a helper object, and in many cases you would not want to use it,
but use `Sfi.Listable:Listable` directly.

| Setting | Description | Defaults |
|---------|-------------|----------|
| wrapClass | Class of a div that wraps the whole object | '' |
| listTitle | Test of a title | '' |
| listTitleClass | Class of a list title | '' |
| archiveLink | Nodepath for archive link | '' |
| archiveLinkTitle | Title of an archive link | '' |
| archiveLinkClass | Classname of an archive link | '' |

# Configuring pagination

Besides enabling pagination in TS object, you must not forget about adding pagination entryIdentifier to all parent cache objects, e.g. like this:

```
prototype(TYPO3.Neos:Page).@cache.entryIdentifier.pagination = ${request.pluginArguments.listable-paginate.currentPage}
root.@cache.entryIdentifier.pagination = ${request.pluginArguments.listable-paginate.currentPage}
```

To make urls pretty, see [Routes.yaml](https://github.com/sfi-ru/Sfi.Listable/blob/master/Configuration/Routes.yaml).
