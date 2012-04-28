=== Mini Loops ===
Contributors: trepmal
Tags: recent, recent posts, most recent, category posts, thumbnail, loop, widget, shortcode, template tag
Donate link: http://kaileylampert.com/donate/
Requires at least: 3.1
Tested up to: 3.3
Stable tag: 1.0.1

Get recent posts, posts from categories, and more. Display as widget or with shortcodes and template tags.

== Description ==
Widget for mini post loops.

Show most recent posts, posts from categories, and more. Can be displayed via widgets, shortcodes, and template tags.

This is a new release, please report bugs to trepmal (at) gmail (dot) com before leaving a poor review. Thanks

== Installation ==

1. Download the zip file and extract the contents.
2. Upload the 'mini-loops' folder to your plugin directory (default: wp-content/plugins/).
3. Activate the plugin through the 'plugins' page in WP.
4. See 'Appearance'->'Widgets' to place it on your sidebar. Set the settings.

== Frequently Asked Questions ==

= How can I exclude categories? =
List its ID as a negative number.

= My site broke after customizing the excerpt. What happened? =
Did you set the strip_tags attribute to false? (`[excerpt strip_tags=0]`). If tags haven't been stripped, it may have cut off the text before an HTML tag was properly closed, thus breaking the page. Setting strip_tags to false should only be used if you're carefully managing the excerpt's output.

= Why can't I add an ID to the element in 'Before Items'? =
There are limitations on the tag or tag/attribute combiniations allowed by WordPress. However, these can be overcome with a few lines of code.

To allow an additional tag, use this: 

`add_filter( "admin_init", "allowed_tags" );
function allowed_tags() {
	global $allowedposttags;
	$allowedposttags["video"] = array();
}`

To allow additional attributes for any tag, use the following code:

`add_filter( "admin_init", "allowed_tags" );
function allowed_tags() {
	global $allowedposttags;
	$allowedposttags["video"]["src"] = array();
	$allowedposttags["video"]["type"] = array();
	$allowedposttags["video"]["poster"] = array();
}`

If you're using a custom theme, you can add this code the its `functions.php` file. Otherwise, you may want to create your own plugin to hold these modifications.

See [this post](http://justintadlock.com/archives/2011/02/02/creating-a-custom-functions-plugin-for-end-users) for instructions on creating your own plugin.

== Screenshots ==

1. Widget Options
2. Sample Format 1 (see Other Notes)
3. Sample Format 2 (see Other Notes)
4. Sample Format 3 (see Other Notes)

== Other Notes ==

Explanation of options:

**Title:** Your recent posts widget's title on your sidebar.
`title="Recent Posts"`

**Title URL:** The page the title should link to.
`title_url="/blog/"`

**Number of Posts:** Number of posts to be displayed
`number_posts=3`

**Post Offset:** Number of posts to skip before displaying the list
`post_offset=0`

**Post Type:** Post type to display
`post_type=post`

**Post Status:** Post status to display. Primarily useful to show upcoming (future) posts. But be creative!
`post_status=publish`

**Order By:** What order the posts should be displayed in
`orderby=date`

**Order:** Ascending (good for order by title) or Descending (good for order by date)
`order=DESC`

**Show posts in reverse order?** Perhaps you want the 3 most recent posts, but you want the oldest of those to be displayed first. If so, check this.
`reverse_order=0`

**Ignore sticky posts?** Treat sticky posts as normal posts. I recommend ignoring, or the number of posts displayed may be inconsistent.
`ignore_sticky=1`

**Exclude sticky posts?** Don't show sticky posts at all.
`exclude_sticky=0`

**Only sticky posts?** Show only sticky posts.
`only_sticky=0`

**Categories:** Comma separated list of category IDs to pull from. Use negative ID numbers to exclude a category.

**Tags:** Comma separated list of tag IDs to pull from. Use negative ID numbers to exclude a tag.

**Custom Taxonomies:** A clunky way to support custom taxonomies. To include terms 5, 6, 9 from taxonomy "Genre" do this:
`tax="genre=5,6,9"`

**Custom Fields:** For listing posts that have certain meta data. To list posts that have a custom field 'favorite_color' with a value of 'blue' do this:
`custom_fields="favorite_color=blue"`

**Exclude Posts:** A comma separated list of post IDs to exclude.

**Before Item:** Text/HTML to insert before the post list

**After Item:** Text/HTML to insert after the post list

**Item Format:**
HTML and shortcodes to format each item

= Shortcodes =
* [ml_title]
* [ml_url]
* [ml_excerpt] Attributes: length (100), wlength (0), after ('...'), space_between (0), after_link (1), custom (0), strip_tags (1), strip_shortcodes (1)
  * length = excerpt length in characters (0 for none, -1 for full length)
  * wlength = excerpt length in words
  * after = what to show after the excerpt
  * space_between = force space between excerpt and 'after'
  * after_link = make the 'after' link to the post
  * custom = 1 to default/customized excerpts, 0 to trim by lentgh
  * strip_tags = 1 to strip HTML tags, 0 to keep. **CAREFUL:** it is not generally recommended to keep the tags. Character excerpts may break tags, and thus break an entire page's layout.
  * strip_shortcodes = 1 to strip shortcodes, 0 to keep
  * up_to_more = 1 to get everything up to the `<!--more-->` tag (the 'more' text), if it exists, otherwise use char/word limit excerpt. 0 use char/word limited excerpt
  * after_with_more = (with up_to_more) 1 to use the 'after' text with the 'more' text, 0 to add nothing after the 'more' text
* [ml_content]
* [ml_comment_count]
* [ml_author]
* [ml_author_link]
* [ml_field] Attributes: name, single (1), separator (', '), reverse (0)
  * name = custom field name
  * single = 1 get single value, 0 get all values matching name
  * separator = string to separate each value
  * reverse = 0 default order, 1 reverse display order
* [ml_taxonomy] Attributes: taxonomy, separator (', '), link (0), justone (0), reverse (0)
  * taxonomy = taxonomy slug
  * separator = string to separate each term
  * link = 1 to link categories to their archive page, 0 for no links
  * justone = 1 to show just first category, 0 to show all
  * reverse = 0 default order, 1 reverse display order
* [ml_tax] Alias to [ml_taxonomy]
* [ml_category] Shortcut for [tax taxonomy=category]
* [ml_tag] Shortcut for [tax taxonomy=post_tag]
* [ml_date] Attributes: format ('F j, Y')
  * format = PHP-style date format
* [ml_class] Attributes: class
  * class = classes to display in addition to the traditional post classes
* [ml_image] Attributes: from, cfname, class, width (50), height (50), crop, fallback
  * from (options: thumb, attached, customfield, first)
     * *from* 'thumb' post thumbnail/featured image `[ml_image from=thumb]`
     * *from* 'attached' first attached image `[ml_image from=attached]`
     * *from* 'customfield' get from custom field `[ml_image from=customfield]`
     * *from* 'first' first image in post `[ml_image from=first]`
  * cfname = custom field to use if from=customfield `[ml_image from=customfield cfname=thumbnail]`
  * class = class for image
  * width = width of image
  * height = height of image
  * crop = 1 to crop, 0 to scale (not implemented yet)
  * fallback = URL of image to use if 'from' doesn't return anything
  * cache = set to 'clear' to generate new thumbnails. It is not recommended that you leave this option on. `[ml_image from=thumb cache=clear]`
  
Inside of Item Format, shortcodes can be used without the `ml_` prefix.

= Sample Item Formats =

Format 1: http://s.wordpress.org/extend/plugins/mini-loops/screenshot-2.png

(before: `<ul>` after: `</ul>`)
`<li class="[class]"><p><a href="[url]">[image from=customfield 
cfname=image width=50 height=50 class=alignright 
fallback='http://dummyimage.com/50'][title]</a><br />
[excerpt wlength=30 space_between=1 after="..." after_link=1]<br /><br />
By [author] on [date format="n/j/y"]</p></li>`


Format 2: http://s.wordpress.org/extend/plugins/mini-loops/screenshot-3.png

(before: `<ul>` after: `</ul>`)
`<li class="[class]"><p>[date format="F j, Y"]<br /><a href="[url]">
[image from=customfield cfname=image width=180 height=100 
class=aligncenter fallback='http://placekitten.com/180/100']</a>
[excerpt length=90 space_between=1 after="..." after_link=1]</p></li>`

Format 3: http://s.wordpress.org/extend/plugins/mini-loops/screenshot-4.png

(before: -- after: --)
`<p class="[class]" style="text-align:center"><a href="[url]">[title]<br />
[image from=customfield cfname=image width=140 height=140 
class=aligncenter fallback='http://placepuppy.it/200/300&text=++woof++']</a></p>`

== Template Tag ==
use `miniloops( $args )` or `get_miniloops( $args )`

Like WordPress function, the 'get_' variant will simply return the results.

Here are the acceptable arguments and their default values:

`$args = array(
		'title' => __( 'Recent Posts', 'mini-loops' ),
		'hide_title' => 0,
		'title_url' => '',
		'number_posts' => 3,
		'post_offset' => 0,
		'post_type' => 'post',
		'post_status' => 'publish',
		'order_by' => 'date',
		'order' => 'DESC',
		'reverse_order' => 0,
		'shuffle_order' => 0,
		'ignore_sticky' => 1,
		'only_sticky' => 0,
		'exclude_current' => 1,
		'categories' => '',
		'tags' => '',
		'tax' => '',
		'custom_fields' => '',
		'exclude' => '',
		'before_items' => '<ul>',
		'item_format' => '<li><a href="[url]">[title]</a><br />[excerpt]</li>',
		'after_items' => '</ul>',
		);
get_miniloops( $args );`

= Shortcode =
`[miniloop]`

Use with all args listed above  
e.g. `[miniloop number_posts=10]`

**Exception - 'item_format' must be handled differently**

New way (since v0.9):

Create a custom field named `ml_format` and save the item format there. Then adjust your `[miniloop]` shortcode  
e.g. `[miniloop number_posts=10][ml_format][/miniloop]`

If needed, you can change the custom field. Just pass the name of the new custom field to the `[ml_format]` shortcode  
e.g. `[miniloop number_posts=10][ml_format name="new_field"][/miniloop]`

Old way:

'item_format' must go into the content of the shortcode, and square brackets must be replaced with curly brackets.  
e.g. `[miniloop number_posts=10]{title}by {author}<br />[/miniloop]`

Also, if you are using html inside the item_format, you must add this into the HTML editor, else your markup will be rendered, not parsed

= Planned =
* true image cropping for remote images

== Other Languages ==
* French (Thanks [@maoneetag](http://twitter.com/maoneetag))

= Bilingual? =
Send your mo/po files to me at trepmal (at) gmail.com

== Upgrade Notice ==

= 1.0 =
New: get from first category if viewing single post.

= 0.9 =
New: multisite support, better image handling.

= 0.8 =
New: new item format options (custom fields and taxonomies).

= 0.7 =
New: more excerpt options

= 0.6 =
New: get posts from current category (if archive) option.

= 0.5 =
Real image croping for thumbnails and several other new features. See Changelog.

= Version 0.5 =

== Changelog ==

= Version 1.1 =
* New: Changed Before/After Items inputs to textareas for easier modifying if there is a lot of markup.
* New: Filters for the Before/After Items content. `miniloops_{before|after}_items_format` $query arguments passed to it. See source for more details.
* New: "Mini Mini Loops" widget. Simplified 'Recent Posts' widget for typical usage - only 3 options.
* New: Filter for altering query. `miniloops_query` See source for more details.
* New: Post author field.
* New: Get posts from current author (if viewing single post or author archive).
* New: 'length' 'before' 'after' parameters for [title] and [url] shortcode. Length is number of characters, processed prior to 'before' and 'after'.
* New: Maximum age field. Only show posts from last X number of days. Thanks bluey80.
* New: Meta value (alpha and numerical) ordering.
* New: [post_type] [post_type_archive_link]. Great for Before/After Item formats.
* New: 'Any' option for post type.
* Fix: Missed marking some strings for translation.
* Fix: Markup mixup for some selected options.
* Improved: fullpath used in include() for better compatibility.

= Version 1.0.1 =
* Fix: Multiple tag bug. Only first was being recognized, now correctly accepts all. Thanks Ozias.
 
= Version 1.0 =
* New: Exclude sticky posts option.
* New: Get posts from first current category (if single).
 
= Version 0.9 =
* Fix: Prevents error from being displayed if image can't be resized.
* New: Improved support for multisite use.
* New: Improved [miniloop] shortcode. Editor tries to hard to "fix" the user-provided item format. Now you can save the format in a custom field. See Other Notes.

= Version 0.8 (2011.10.31) =
* Added French Translation files. (Thanks [@maoneetag](http://twitter.com/maoneetag))

= Version 0.8 =
* New: [ml_field] shortcode, [ml_category] shortcode, [ml_tag] shortcode, [ml_taxonomy] shortcode.
* Fix: strip slashes from widget title.

= Version 0.7 =
* New: more excerpt options (use automated/custom excerpts rather than trim by length, option to bypass tag/shortcode stripping). Please report issues.
* Fix: stipping slashes for before/after item during output.

= Version 0.6 =
* New (sorta): shortcode option `[miniloop]` (see Other Notes for usage). Why "sorta"? shortcode has existed the whole time, I only just now added some docs.
* New: get posts from current category (if archive).
* New: custom taxonomies, if only 1 ID given, and it's negative, treat it like "NOT IN".
* Fix: added missing echo in instructions.
* General code optimization.

= Version 0.5 =
* New: get only sticky posts.
* New: shuffle order.
* New: [ml_author_link] shortcode.
* New: [ml_comment_count] shortcode.
* New: thumbnail cropping for local images.
* New: Ready for localization.

= Version 0.4 =
* New image option: get first attached image (great for galleries!).
* Bug fix: broken image if no fallback is set.

= Version 0.3 =
* Bug fix: post status works now.

= Version 0.2 =
* Improvements: hide title, link title, exclude current single post options.

= Version 0.1 =
* Initial release version.
