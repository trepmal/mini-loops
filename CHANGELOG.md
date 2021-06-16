## Changelog

### Version 1.4.1

- Fix: Corrects sloppy shortcode fixes.
- Fix: Mini-Mini-Loops widget default settings.

### Version 1.4

- Long-overdue cleanup.

### Version 1.3.1

- Fix: Widget title links

### Version 1.3

- Fix: Widget warnings since 4.3
- New: 'crop' parameter for the image tag
- New: Allow any shortcodes in item format
- New: Automatically clear thumbnail cache on image change. props @om4james
- Fix: Use widget_title filter

### Version 1.2

- Fix: undefined index notice if zero posts match query
- Fix: markup errors in widget. Corrects save issue regarding order
- Fix: reset postdata instead of query
- New: BETA - use `[ba_archive before='' after='']` shortcode to insert an author/taxonomy link. For use with before|after_items fields.
- Fix: removed deprecated function for thumbnail creation
- New: fallback 'from' options for [image]. [image from="thumb,first"] - If no post thumbnail (featured image), get first image from post
- New: [author_avatar] with optional parameters [author_avatar size=92 default='' alt=0]
- New: Multisite - Show posts from sister-sites (on same network). REQUIRES ADD ON: https://gist.github.com/trepmal/5073067
- New: Ajax-paging - View prev/next set of posts in widget. REQUIRES ADD ON: https://gist.github.com/trepmal/5073756

### Version 1.1.2

- Fix: imbalanced tags if zero posts match query

### Version 1.1

- New: Changed Before/After Items inputs to textareas for easier modifying if there is a lot of markup.
- New: Filters for the Before/After Items content. `miniloops_{before|after}_items_format` $query arguments passed to it. See source for more details.
- New: "Mini Mini Loops" widget. Simplified 'Recent Posts' widget for typical usage - only 3 options.
- New: Filter for altering query. `miniloops_query` See source for more details.
- New: Post author field.
- New: Get posts from current author (if viewing single post or author archive).
- New: 'length' 'before' 'after' parameters for [title] and [url] shortcode. Length is number of characters, processed prior to 'before' and 'after'.
- New: Maximum age field. Only show posts from last X number of days. Thanks bluey80.
- New: Meta value (alpha and numerical) ordering.
- New: [post_type] [post_type_archive_link]. Great for Before/After Item formats.
- New: 'Any' option for post type.
- New: Alt text support for thumbnails.
- Fix: Missed marking some strings for translation.
- Fix: Markup mixup for some selected options.
- General code clean up and improvements, such as full path used in include().

### Version 1.0.1

- Fix: Multiple tag bug. Only first was being recognized, now correctly accepts all. Thanks Ozias.

### Version 1.0

- New: Exclude sticky posts option.
- New: Get posts from first current category (if single).

### Version 0.9

- Fix: Prevents error from being displayed if image can't be resized.
- New: Improved support for multisite use.
- New: Improved [miniloop] shortcode. Editor tries to hard to "fix" the user-provided item format. Now you can save the format in a custom field. See Other Notes.

### Version 0.8 (2011.10.31)

- Added French Translation files. (Thanks [@maoneetag](http://twitter.com/maoneetag))

### Version 0.8

- New: [ml_field] shortcode, [ml_category] shortcode, [ml_tag] shortcode, [ml_taxonomy] shortcode.
- Fix: strip slashes from widget title.

### Version 0.7

- New: more excerpt options (use automated/custom excerpts rather than trim by length, option to bypass tag/shortcode stripping). Please report issues.
- Fix: stipping slashes for before/after item during output.

### Version 0.6

- New (sorta): shortcode option `[miniloop]` (see Other Notes for usage). Why "sorta"? shortcode has existed the whole time, I only just now added some docs.
- New: get posts from current category (if archive).
- New: custom taxonomies, if only 1 ID given, and it's negative, treat it like "NOT IN".
- Fix: added missing echo in instructions.
- General code optimization.

### Version 0.5

- New: get only sticky posts.
- New: shuffle order.
- New: [ml_author_link] shortcode.
- New: [ml_comment_count] shortcode.
- New: thumbnail cropping for local images.
- New: Ready for localization.

### Version 0.4

- New image option: get first attached image (great for galleries!).
- Bug fix: broken image if no fallback is set.

### Version 0.3

- Bug fix: post status works now.

### Version 0.2

- Improvements: hide title, link title, exclude current single post options.

### Version 0.1

- Initial release version.
