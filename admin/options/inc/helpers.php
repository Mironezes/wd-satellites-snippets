<?php
// Picks random image from predifined lists
function rand_image_from_list($post_id)
{
    $category = get_the_category($post_id);

    if (!empty($category))
    {

        $option_postfix = preg_replace('/\-+/', '_', strtolower($category[0]->slug));

        $option = get_option('wdss_featured_images_list_' . $option_postfix, '');

        if ($option)
        {
            $images_ids_arr = explode(',', $option);
            $rand_index = array_rand($images_ids_arr);
            $image_id = intval($images_ids_arr[$rand_index]);
            set_post_thumbnail($post_id, $image_id);
        }
    }
}

function attach_first_post_image($post_id, $file, $desc = 'Image')
{
    global $debug; // by default: true
    if (!function_exists('media_handle_sideload'))
    {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
    }

    // Download image by given url
    $tmp = download_url($file);

    // Prepares data to be attach in WP Media
    $file_array = ['name' => basename($file) , 'tmp_name' => $tmp];

    // If error then removes tmp file
    if (is_wp_error($tmp))
    {
        $file_array['tmp_name'] = '';
        if ($debug) echo 'Error: there`s no tmp file! <br />';
    }

    // If Debug mode is enabled then shows details
    // if( $debug ) {
    //   echo 'File array: <br />';
    //   var_dump( $file_array );
    //   echo '<br /> Post id: ' . $post_id . '<br />';
    // }
    // Sets data as new media file in WP Media
    $id = media_handle_sideload($file_array, $post_id, $desc);

    // Checks if there`s any errors
    if (is_wp_error($id))
    {
        // var_dump( $id->get_error_messages() );
        return false;
    }
    else
    {
        update_post_meta($post_id, '_thumbnail_id', $id);
        return true;
    }

    // Removes tmp file
    @unlink($tmp);
}


// Checks image dimensions
function check_image_size($url)
{
    list($width, $height) = getimagesize($url);

    if (isset($width) && isset($height) && $width > 100)
    {
        return true;
    }
    return false;
}

// URL Status Code Checker
function check_url_status($url, $condition = null)
{

    $meeting_conditions = true;

    if ($condition)
    {
        switch ($condition)
        {
            case 'local-only':

                if (!preg_match('/' . $_SERVER['SERVER_NAME'] . '/'))
                {
                    $meeting_conditions = false;
                }
            break;

            default:
            break;
        }
    }

    // Checks the existence of URL
    if ($meeting_conditions && @fopen($url, "r"))
    {
        return true;
    }
    else
    {
        return false;
    }
}

// Auto width/height attributes
function set_image_dimension($content)
{

    $buffer = $content;

    // Get all images
    $pattern1 = '/<img(?:[^>])*+>/i';
    preg_match_all($pattern1, $content , $first_match);

    $all_images = array_merge($first_match[0]);
    foreach ($all_images as $image)
    {

        $tmp = $image;

        // Removing existing width/height attributes
        $clean_image = preg_replace('/\swidth="(\d*(px%)?)"(\sheight="(\w+)")?/', '', $tmp);

        if ($clean_image)
        {

            // Get link of the file
            preg_match('/src=[\'"]([^\'"]+)/', $clean_image, $src_match);

            // Compares src with banned hosts
            $in_block_list = false;
            $exceptions = get_option('wdss_excluded_hosts_dictionary', '');
            // chemistryland.com, fin.gc.ca, support.revelsystems.com
            foreach ($exceptions as $exception)
            {
                if (strpos($src_match[1], $exception) !== false)
                {
                    $in_block_list = true;
                }
            }

            // If image is BLOB encoded
            if (!empty(strpos($src_match[0], 'data:image')))
            {

                $image_url = $src_match[1];

                $binary = base64_decode(explode(',', $image_url) [1]);

                if (!getimagesizefromstring($binary)) return;

                $image_data = getimagesizefromstring($binary) ? getimagesizefromstring($binary) : false;

                if ($image_data)
                {
                    $width = $image_data[0];
                    $height = $image_data[1];
                }
            }

            // Regular src case
            else
            {
                // If image`s host in block list then remove it
                if ($in_block_list)
                {
                    $buffer = str_replace($tmp, '', $buffer);
                    return $buffer;
                }
                // If src doesn`t contains SERVER NAME then add it
                if (strpos($src_match[1], 'wp-content') && strpos($src_match[1], 'https') === false)
                {
                    $src_match[1] = 'https://' . $_SERVER['SERVER_NAME'] . $src_match[1] . '';
                }
                // If image src returns 200 status then get image size
                if (check_url_status($src_match[1]))
                {
                    list($width, $height) = getimagesize($src_match[1]);
                }
            }

            // Checks if width & height are defined
            if (!empty($width) && !empty($height))
            {
                $dimension = 'width="' . $width . '" height="' . $height . '" ';

                // Add width and width attribute
                $image = str_replace('<img', '<img loading="lazy" ' . $dimension, $clean_image);

                // Replace image with new attributes
                $buffer = str_replace($tmp, $image, $buffer);
            }
            else
            {
                $buffer = str_replace($tmp, '', $buffer);
            }
        }
        elseif (!check_url_status($src_match[1]))
        {
            $buffer = str_replace($tmp, '', $buffer);
        }
    }
    return $buffer;
}

// Filters post content from validation errors
function regex_post_content_filters($content)
{
    $pattern1 = '/<div itemscope="" itemprop="mainEntity" itemtype="https:\/\/schema\.org\/Question">\n?<div itemprop="name">\n?(<h3>.*<\/h3>?)\n?<\/div>\n?<div itemscope="" itemprop="acceptedAnswer" itemtype="https:\/\/schema\.org\/Answer">\n?<div itemprop="text">\n(<p>.*<\/p>?)\n<\/div>\n?<\/div>\n?<\/div>/';

    $pattern2 = '/<div itemScope itemProp="mainEntity" itemType="https:\/\/schema\.org\/Question">\n?\s*<div itemProp="name">\n?\s*(<h2>.*<\/h2>?)\n?\s*<\/div>\n?\s*?<div itemScope itemProp="acceptedAnswer" itemType="https:\/\/schema\.org\/Answer">\n?\s*<div itemProp="text"><p>.*<\/p><\/div>\s*<\/div><\/div>/';

    $content = preg_replace($pattern1, '${1}<br>${2}', $content);
    $content = preg_replace($pattern2, '${1}<br>${2}', $content);

    return $content;
}

// Adds alts for post content images
function alt_singlepage_autocomplete($id, $content)
{
    $post = get_post($id);
    $old_content = $content;

    preg_match_all('/<img[^>]+>/', $content, $images);

    if (!is_null($images))
    {
        foreach ($images[0] as $index => $value)
        {
            if (!preg_match('/alt=/', $value))
            {
                $new_img = str_replace('<img', '<img alt="' . esc_attr($post->post_title) . '"', $images[0][$index]);
                $content = str_replace($images[0][$index], $new_img, $content);
            }
            else if (preg_match('/alt=["\']\s?["\']/', $value))
            {
                $new_img = preg_replace('/alt=["\']\s?["\']/', 'alt="' . esc_attr($post->post_title) . '"', $images[0][$index]);
                $content = str_replace($images[0][$index], $new_img, $content);
            }
        }
    }

    if (empty($content))
    {
        return $old_content;
    }

    return $content;
}

