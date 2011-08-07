=== Mini Loops ===
Contributors: trepmal
Tags: recent, recent posts, most recent, category posts, thumbnail, loop, widget, shortcode, template tag
Donate link: http://kaileylampert.com/donate/
Requires at least: 3.1
Tested up to: 3.2.1
Stable tag: trunk

Get recent posts, posts from categories, and more. Display as widget or with shortcodes and template tags.

== Description ==
Widget for mini post loops.

Show most recent posts, posts from categories, and more. Can be displayed via widgets, shortcodes, and template tags.

This is a new release, please report bugs to trepmal (at) gmail (dot) com before leaving a poor review. Thanks

== Installation ==

= Installation =
1. Download the zip file and extract the contents.
2. Upload the 'mini-loops' folder to your plugin directory (default: wp-content/plugins/).
3. Activate the plugin through the 'plugins' page in WP.
4. See 'Appearance'->'Widgets' to place it on your sidebar. Set the settings.

== Frequently Asked Questions ==

= How can I exclude categories? =
List its ID as a negative number.

== Screenshots ==

1. Widget Options
2. Sample Format 1 (see Other Notes)
3. Sample Format 2 (see Other Notes)
4. Sample Format 3 (see Other Notes)

== Other Notes ==

Explanation of options:

**Title:** Your recent posts widget's title on your sidebar.

**Title Link:** The page the title should link to.

**Number of Posts:** Number of posts to be displayed

**Post Offset:** Number of posts to skip before displaying the list

**Post Type:** Post type to display

**Post Status:** Post status to display. Primarily useful to show upcoming (future) posts. But be creative!

**Order By:** What order the posts should be displayed in

**Order:** Ascending (good for order by title) or Descending (good for order by date)

**Show posts in reverse order?** Perhaps you want the 3 most recent posts, but you want the oldest of those to be displayed first. If so, check this.

**Ignore sticky posts?** I recommend ignoring, or the number of posts displayed may be inconsistent.

**Categories:** Comma separated list of category IDs to pull from. Use negative ID numbers to exclude a category.

**Tags:** Comma separated list of tag IDs to pull from. Use negative ID numbers to exclude a tag.

**Custom Taxonomies:** A clunky way to support custom taxonomies. To include terms 5, 6, 9 from taxonomy "Genre" do this:
`genre=5,6,9`

**Custom Fields:** For listing posts that have certain meta data. To list posts that have a custom field 'favorite_color' with a value of 'blue' do this:
`favorite_color=blue`

**Exclude Posts:** A comma separated list of post IDs to exclude.

**Before Item:** Text/HTML to insert before the post list

**After Item:** Text/HTML to insert after the post list

**Item Format:**
HTML and shortcodes to format each item

= Shortcodes =
* [ml_title]
* [ml_url]
* [ml_excerpt] Attributes: length (100), wlength (0), after ('...'), space_between (0), after_link (1)
  * length = excerpt length in characters
  * wlength = excerpt length in words
  * after = what to show after the excerpt
  * space_between = force space between excerpt and 'after'
  * after_link = make the 'after' link to the post
* [ml_content]
* [ml_comment_count]
* [ml_author]
* [ml_author_link]
* [ml_date] Attributes: format ('F j, Y')
  * format = PHP-style date format
* [ml_class] Attributes: class
  * class = classes to display in addition to the traditional post classes
* [ml_image] Attributes: from, cfname, class, width (50), height (50), crop, fallback
  * from (options: thumb, attached, customfield, first)
     * *from* 'thumb' post thumbnail/featured image
     * *from* 'attached' first attached image
     * *from* 'customfield' get from custom field
     * *from* 'first' first image in post
  * cfname = custom field to use if from=customfield
  * class = class for image
  * width = width of image
  * height = height of image
  * crop = 1 to crop, 0 to scale (not implemented yet)
  * fallback = URL of image to use if 'from' doesn't return anything
  * cache = set to 'clear' to generate new thumbnails
  
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

= Planned =
* true image cropping for remote images

== Upgrade Notice ==

= 0.5 =
Real image croping for thumbnails and several other new features. See Changelog.

= Version 0.5 =

== Changelog ==

= Version 0.5 =
* New: get only sticky posts
* New: shuffle order
* New: [ml_author_link] shortcode
* New: [ml_comment_count] shortcode
* New: thumbnail cropping for local images
* New: Ready for localization

= Version 0.4 =
* New image option: get first attached image (great for galleries!)
* Bug fix: broken image if no fallback is set

= Version 0.3 =
* Bug fix: post status works now

= Version 0.2 =
* Improvements: hide title, link title, exclude current single post options

= Version 0.1 =
* Initial release version.
