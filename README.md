# joomla-xbArticleMan
xbArticle Manager is a Joomla 3 component to provide some additional tools for backend administration of Joomla content. It provides views for finding Images, Links, Shortcodes and Tags in, or associated with, com_content articles.

**Version 2.1.0** is a major update with many minor and major improvements. Work started on it back in 2019, but it was put aside for nearly 4 years before finally being picked up again. 

*NB It is still not Joomla5 compliant, although most of the code has been updated. I still run J3 sites exclusively, with no plans to migrate to J5 as I have too much work invested in custom extensions for particular sites to make it worth the effort.*

*Having said that I will now respond positively to requests for general purpose extensions like this to be considered for updating.*

Currently xbArticleMan offers six views in the backend:

1. **Dashboard** - a summary of the counts of articles, tags, images, links and shortcodes
2. **Articles:tags** - details of the tags assigned to each article
3. **Articles:links** - shows the &lt;a ...&gt;...&lt;<&gt; tags in the content, and the related links fields for each article
4. **Articles:images** - shows the &lt;img .../&gt;tags in the content, and the intro/full image fields for each article
5. **Articles: Shortcodes** - lists all of the plugin shortcodes found in each article
6. **Article Edit simplified** - a simplified article edit screen which allows editing of title, alias, category, tags, and the image and link fields for an article.

Install as normal for a Joomla extension. Download the latest zip file from the website [crosborne.uk/component/phocadownload/file/25-xbarticleman](https://crosbornel.uk/component/phocadownload/file/25-xbarticleman) 

Currently proposed new features are:

- Categories list view showing the assignment of articles to categories (ie a list of com_content category names with the articles for each category listed)
- Tag detail view listing all the articles assigned to a single tag
