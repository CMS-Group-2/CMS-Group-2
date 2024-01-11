<?php
/**
 * Theme only works in WordPress 4.4 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.4-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
}

locate_template(array('library/options.php'), true);
locate_template(array('library/navigation.php'), true);
locate_template(array('library/sidebars.php'), true);
locate_template(array('library/widgets.php'), true);
locate_template(array('library/seo.php'), true);
locate_template(array('library/app.php'), true);
locate_template(array('library/breadcrumbs.php'), true);


if (!function_exists('theme_setup')) {
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     *
     * Create your own theme_setup() function to override in a child theme.
     */
    function theme_setup() {
        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for custom logo.
         */
        add_theme_support('custom-logo', array(
            'height' => 240,
            'width' => 240,
            'flex-height' => true,
        ));

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
         */
        add_theme_support('post-thumbnails');
        set_post_thumbnail_size(1200, 9999);

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));

        /*
         * Enable support for Post Formats.
         *
         * See: https://codex.wordpress.org/Post_Formats
         */
        add_theme_support('post-formats', array(
            'aside',
            'image',
            'video',
            'quote',
            'link',
            'gallery',
            'status',
            'audio',
            'chat',
        ));

        // Indicate widget sidebars can use selective refresh in the Customizer.
        add_theme_support('customize-selective-refresh-widgets');

        // Add theme version for compatibility with plugin
        $GLOBALS['npThemeVersion'] = '4.2.6';
    }

    add_action('after_setup_theme', 'theme_setup');
}


/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 */
function theme_javascript_detection() {
    echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action('wp_head', 'theme_javascript_detection', 0);

/**
 * Enqueues scripts and styles.
 */
function theme_scripts() {
    $version = wp_get_theme()->get('Version');

    // Theme stylesheet.
    wp_enqueue_style('theme-style', get_stylesheet_uri(), array(), $version);
    wp_enqueue_style('theme-media', get_template_directory_uri() . '/css/media.css', array('theme-style'), $version);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    if (is_singular() && wp_attachment_is_image()) {
        wp_enqueue_script('theme-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array('jquery'), '20160816');
    }

    wp_localize_script('theme-script', 'screenReaderText', array(
        'expand' => __('expand child menu', 'theme'),
        'collapse' => __('collapse child menu', 'theme'),
    ));

    // remove plugin's scripts and styles
    wp_dequeue_style("nicepage-style");
    wp_dequeue_style("nicepage-responsive");
    wp_dequeue_style("nicepage-media");
    wp_dequeue_script("nicepage-script");

    if (theme_get_option('include_jquery')) {
        wp_dequeue_script("nicepage-jquery");
        wp_register_script('theme-jquery', get_template_directory_uri() . '/js/jquery.js', array(), '1.9.1');
        wp_enqueue_script('theme-jquery');

        wp_enqueue_script('theme-script', get_template_directory_uri() . '/js/script.js', array('theme-jquery'), $version);
    } else {
        wp_enqueue_script('theme-script', get_template_directory_uri() . '/js/script.js', array('jquery'), $version);
    }
}
add_action('wp_enqueue_scripts', 'theme_scripts', 1002);


/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array (Maybe) filtered body classes.
 */
function theme_body_classes($classes) {
    // Adds a class of custom-background-image to sites with a custom background image.
    if (get_background_image()) {
        $classes[] = 'custom-background-image';
    }

    // Adds a class of group-blog to sites with more than 1 published author.
    if (is_multi_author()) {
        $classes[] = 'group-blog';
    }

    // Adds a class of no-sidebar to sites without active sidebar.
    $classes[] = 'no-sidebar';

    // Adds a class of hfeed to non-singular pages.
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }

    $classes[] = 'u-body'; // need for typography in other pages
    return $classes;
}
add_filter('body_class', 'theme_body_classes');

/**
 * Converts a HEX value to RGB.
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 *
 * @return array Array containing RGB (red, green, and blue) values for the given
 *               HEX code, empty array otherwise.
 */
function theme_hex2rgb($color) {
    $color = trim($color, '#');

    if (strlen($color) === 3) {
        $r = hexdec(substr($color, 0, 1) . substr($color, 0, 1));
        $g = hexdec(substr($color, 1, 1) . substr($color, 1, 1));
        $b = hexdec(substr($color, 2, 1) . substr($color, 2, 1));
    } else if (strlen($color) === 6) {
        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));
    } else {
        return array();
    }

    return array('red' => $r, 'green' => $g, 'blue' => $b);
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *                      values in pixels (in that order).
 *
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function theme_content_image_sizes_attr($sizes, $size) {
    $width = $size[0];

    840 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 62vw, 840px';

    if ('page' === get_post_type()) {
        840 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
    } else {
        840 > $width && 600 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 61vw, (max-width: 1362px) 45vw, 600px';
        600 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
    }

    return $sizes;
}
add_filter('wp_calculate_image_sizes', 'theme_content_image_sizes_attr', 10, 2);

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for post thumbnails
 *
 * @param array $attr Attributes for the image markup.
 * @param int   $attachment Image attachment ID.
 * @param array $size Registered image size or flat array of height and width dimensions.
 *
 * @return string A source size value for use in a post thumbnail 'sizes' attribute.
 */
function theme_post_thumbnail_sizes_attr($attr, $attachment, $size) {
    if ('post-thumbnail' === $size) {
        $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 88vw, 1200px';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'theme_post_thumbnail_sizes_attr', 10, 3);

/**
 * Modifies tag cloud widget arguments to have all tags in the widget same font size.
 *
 * @param array $args Arguments for tag cloud widget.
 *
 * @return array A new modified arguments.
 */
function theme_widget_tag_cloud_args($args) {
    $args['largest'] = 1;
    $args['smallest'] = 1;
    $args['unit'] = 'em';
    return $args;
}
add_filter('widget_tag_cloud_args', 'theme_widget_tag_cloud_args');


/**
 * Sets the content width in pixels, based on the theme's design and stylesheet.
 *
 * @global int $content_width
 */
function theme_content_width() {
	$GLOBALS['content_width'] = apply_filters('theme_content_width', 1140);
}
add_action('after_setup_theme', 'theme_content_width');

function theme_widgets_init2() {
    register_sidebar(array(
        'name'          => __('Primary Widget Area', 'theme'),
        'id'            => 'primary',
        'description'   => __('Add widgets here to appear in your sidebar on blog posts and archive pages.', 'theme'),
        'before_widget' => '<widget id="%1$s" name="%1$s" class="widget %2$s">',
        'after_widget'  => '</widget>',
        'after_title'   => '</title>',
        'before_title'  => '<title>',
    ));
}
add_action('widgets_init', 'theme_widgets_init2');

function theme_sidebar($args) {
    ob_start();
    dynamic_sidebar($args['id']);
    $content = ob_get_clean();

    $data = explode('</widget>', $content);
    $widgets = array();
    foreach ($data as $widget) {
        if (!$widget) {
            continue;
        }

        $id = null;
        $name = null;
        $class = null;
        $title = null;

        if (preg_match('/<widget(.*?)>/', $widget, $matches)) {
            if (preg_match('/id="(.*?)"/', $matches[1], $ids)) {
                $id = $ids[1];
            }
            if (preg_match('/name="(.*?)"/', $matches[1], $names)) {
                $name = $names[1];
            }
            if (preg_match('/class="(.*?)"/', $matches[1], $classes)) {
                $class = $classes[1];
            }
            $widget = preg_replace('/<widget[^>]+>/', '', $widget);

            if (preg_match('/<title>(.*)<\/title>/', $widget, $matches)) {
                $title = $matches[1];
                $widget = preg_replace('/<title>.*?<\/title>/', '', $widget);
            }
        }
        $widget = str_replace('<ul class="product-categories">', '<ul>', $widget);

        $widgets[] = array(
            'id' => $id,
            'name' => $name,
            'class' => $class,
            'title' => $title,
            'content' => $widget
        );
    }

    $result = '';
    foreach ($widgets as $widget) {
        $result .= strtr($args['template'], array(
            '{block_content}' => $widget['content'],
            '{block_header}' => $widget['title'],
        ));
    }
    return $result;
}

/**
 * @param  array      $array
 * @param  string|int $key
 * @param  mixed      $default
 * @return mixed
 */
function theme_get_array_value(&$array, $key, $default = false) {
	if (isset($array[$key])) {
		return $array[$key];
	}
	return $default;
}

require_once dirname(__FILE__) . '/library/tgm-activation.php';

if (is_admin()) {
	locate_template(array('library/content-import.php'), true);
}

theme_include_lib('post_templates.php');

function theme_include_lib($name, $dir = 'library') {
    locate_template(array($dir . '/' . $name), true);
}

global $theme_custom_templates;
$theme_custom_templates = array();

theme_include_lib('templates.php', 'templates');

/*
 * Include the template depends on value stored in database
 *
 * @global array $theme_custom_templates
 *
 * @param string $type The type of template (Home, Products, 404, ect)
 * @param string $default_name Name of the template
 */
function theme_load_template($type, $default_name) {
    global $theme_custom_templates;
    $name = theme_get_selected_template($type);
    if (!$name)
        $name = $default_name;

    $path = theme_get_array_value($theme_custom_templates[$type], $name, $theme_custom_templates[$type][$default_name]);
    require_once(get_template_directory() . '/' . $path);
}