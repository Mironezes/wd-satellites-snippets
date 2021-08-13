<?php 

/*
  Plugin Name: WD Sattelites Snippets
  Plugin URI: https://github.com/Mironezes
  Description: Bulk of usefull snippets and options for our sattelites 
  Version: 0.2.6
  Author: Alexey Suprun
  Author URI: https://github.com/Mironezes
  Text Domain: wdss_domain
  Domain Path: /languages
*/

if ( !defined('ABSPATH') ) exit;


define('WDSS_VERSION', '0.2.6');
define('WDSS_DOMAIN', 'wdss_domain');

class WD_Sattelites_Snippets {

  function __construct() {
    add_action( 'admin_menu', array($this, 'wdss_admin_menu') );

    add_action( 'init', array($this, 'wdss_snippets') );

    add_action( 'init', function() {
      if ( is_admin_bar_showing() ) {
        if ( is_admin() ) {
          add_action( 'admin_enqueue_scripts', array( $this, 'wdss_admin_scripts_loader' ) );
        } else {
          add_action( 'wp_enqueue_scripts', array( $this, 'wdss_site_scripts_loader' ) );
        }
  
        add_action( 'admin_bar_menu', array($this, 'wdss_add_adminbar_link'), 100);
      }
    });

    if( get_option('wdss_last_modified_n_304', '0') ) {
      add_action( 'init', array($this, 'wdss_last_modified') );
    }

    if( get_option('wdss_force_lowercase', '0') ) {
      add_action( 'init', array($this, 'wdss_force_lowercase') );
    }    
  }

  function wdss_admin_scripts_loader() {
    wp_enqueue_style( 'wdss-style', plugin_dir_url(__FILE__) . 'style.css', array(), wp_rand() );
    wp_enqueue_script( 'wdss-script', plugin_dir_url(__FILE__) . 'index.js', array(), wp_rand(), true );    
  }

  function wdss_site_scripts_loader() {
    wp_enqueue_style( 'wdss-style', plugin_dir_url(__FILE__) . 'style.css', array(), wp_rand() );
  }
	

  // Last-modifed Snippet
  function wdss_last_modified() {

      function last_modified_default() {
        $LastModified_unix = getlastmod();
        $LastModified = gmdate( "D, d M Y H:i:s \G\M\T", $LastModified_unix );
        $IfModifiedSince = false;
        if ( isset($_ENV['HTTP_IF_MODIFIED_SINCE']) ) {
          $IfModifiedSince = strtotime( substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5) );
        }
        if ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ) {
          $IfModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
        }
        if ( $IfModifiedSince && $IfModifiedSince >= $LastModified_unix ) {
          header( $_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified' );
          exit;
        }
        header( 'Last-Modified: '. $LastModified );

        add_action('wbcr/factory/option_if_modified_since_headers', function($option_value){
          $server_headers = apache_response_headers();
          if (isset($server_headers['Last-Modified'])
            && !empty($server_headers['Last-Modified'])
            && $option_value){
            
            header_remove('Set-Cookie');
          }
            
          return $option_value;
        }, 1);
      }

      function last_modified_polylang() {
        if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( is_admin() ) ) {
          return;
        }
      
        $last_modified = '';
      
        if ( is_singular() ) {
          global $post;
      
          if ( post_password_required( $post ) )
            return;
      
          if ( !isset( $post -> post_modified_gmt ) ) {
            return;
          }
      
          $post_time = strtotime( $post -> post_modified_gmt );
          $modified_time = $post_time;
      
          if ( ( int ) $post -> comment_count > 0 ) {
            $comments = get_comments( array(
              'post_id' => $post -> ID,
              'number' => '1',
              'status' => 'approve',
              'orderby' => 'comment_date_gmt',
                ) );
            if ( !empty( $comments ) && isset( $comments[0] ) ) {
              $comment_time = strtotime( $comments[0] -> comment_date_gmt );
              if ( $comment_time > $post_time ) {
                $modified_time = $comment_time;
              }
            }
          }
      
          $last_modified = str_replace( '+0000', 'GMT', gmdate( 'r', $modified_time ) );
        }
      
        if ( is_archive() || is_home() ) {
          global $posts;
      
          if ( empty( $posts ) ) {
            return;
          }
      
          $post = $posts[0];
      
          if ( !isset( $post -> post_modified_gmt ) ) {
            return;
          }
      
          $post_time = strtotime( $post -> post_modified_gmt );
          $modified_time = $post_time;
      
          $last_modified = str_replace( '+0000', 'GMT', gmdate( 'r', $modified_time ) );
        }
      
        if ( headers_sent() ) {
          return;
        }
      
        if ( !empty( $last_modified ) ) {
          header( 'Last-Modified: ' . $last_modified );
      
          if ( !is_user_logged_in() ) {
            if ( isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) && strtotime( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) >= $modified_time ) {
              $protocol = (isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1');
              header( $protocol . ' 304 Not Modified' );
            }
          }
        }
      }

      if( function_exists('pll_the_languages') ) {
        add_action( 'template_redirect', 'last_modified_polylang' );
      }
      else {
        last_modified_default();
      }
  }

	
  // Force Lowercase URLs Snippet
  function wdss_force_lowercase() {
      $url = $_SERVER['REQUEST_URI'];
      $params = (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
      
      // If URL contains a period, halt (likely contains a filename and filenames are case specific)
      if ( preg_match('/[\.]/', $url) ) {
        return;
      }
      
      // If URL contains a capital letter
      if ( preg_match('/[A-Z]/', $url) ) {
      
        // // If URL contains a '%' - skip, if url decoded
        if (strpos($url, '%') !== false) {
          return;
        }
      
        $lc_url = empty($params)
        ? strtolower($url)
        : strtolower(substr($url, 0, strrpos($url, '?'))).'?'.$params;
        
        // if url was modified, re-direct
        if ($lc_url !== $url) {
          header('Location: '.$lc_url, TRUE, 301);
          exit();
        }
      }
  }

  function wdss_add_adminbar_link($admin_bar) {
    $admin_bar->add_menu([
      'id'    => 'wdss',
      'title' => 'Satellites Snippets',
      'href'  =>  admin_url('admin.php?page=wd-sattelites-snippets'),
      'meta'  => array(
          'title' => 'WD Satellites Snippets'
      ),
    ]);
  }
	
	
  function wdss_snippets() {
    // Auto Generated Alts for images on attach action
    add_action('add_attachment', 'wd_auto_generated_image_alts');
    function wd_auto_generated_image_alts($postID)
    {
        if (wp_attachment_is_image($postID))
        {
            // Grab the Title auto-generated by WordPress
            $ehi_image_title = get_post($postID)->post_title;
    
            // Remove hyphens, underscores, and extra spaces
            $ehi_image_title = preg_replace('%\s*[-_\s]+\s*%', ' ',  $ehi_image_title);
    
            // Capitalize first letter of every word
            $ehi_image_title = ucwords(strtolower($ehi_image_title));
            
            $ehi_image_meta = array(
                'ID'        => $postID,
                'post_title'    => $ehi_image_title, // Image Title
                'post_content'  => $ehi_image_title, // Image Description (Content)
            );
    
            // Update the Alt Text
            update_post_meta($postID, '_wp_attachment_image_alt', $ehi_image_title);
    
            // Update the other fields
            wp_update_post($ehi_image_meta);
        } 
    }


    function gallery_template_to_posts() {
      $post_type_object = get_post_type_object( 'post' );
      $post_type_object->template = array(
          array( 'core/gallery', array(
              'linkTo' => 'media',
          ) ),
      );
    }
    add_action( 'init', 'gallery_template_to_posts' );



    // Auto Featured Image on post saving (from first attached image)
    if( get_option('wdss_auto_featured_image', '0') ) {
      add_action('future_to_publish', 'autoset_featured');
      add_action('draft_to_publish', 'autoset_featured');
      add_action('new_to_publish', 'autoset_featured');
      add_action('pending_to_publish', 'autoset_featured');
      add_action('save_post', 'autoset_featured');
  
      function autoset_featured() {
        global $post;
  
        if( has_post_thumbnail($post->ID) )
          return;
  
        $attached_image = get_children( array(
          'post_parent'=>$post->ID, 'post_type'=>'attachment', 'post_mime_type'=>'image', 'numberposts'=>1, 'order'=>'ASC'
        ) );
  
        if( $attached_image ){
          foreach ($attached_image as $attachment_id => $attachment)
            set_post_thumbnail($post->ID, $attachment_id);
        }
      }
    }


    // Auto Alt Attributes for images
    if( get_option('wdss_auto_alt_attribute', '0') ) {
      function wdss_alt_autocomplete( $content ) {
        global $post;
    
        if ( empty( $post ) ) {
          return $content;
        }
    
        $old_content = $content;
    
        preg_match_all( '/<img[^>]+>/', $content, $images );
    
        if ( ! is_null( $images ) ) {
          foreach ( $images[0] as $index => $value ) {
            if ( ! preg_match( '/alt=/', $value ) ) {
              $new_img = str_replace( '<img', '<img alt="' . esc_attr( $post->post_title ) . '"', $images[0][ $index ] );
              $content = str_replace( $images[0][ $index ], $new_img, $content );
            } else if ( preg_match( '/alt=["\']\s?["\']/', $value ) ) {
              $new_img = preg_replace( '/alt=["\']\s?["\']/', 'alt="' . esc_attr( $post->post_title ) . '"', $images[0][ $index ] );
              $content = str_replace( $images[0][ $index ], $new_img, $content );
            }
          }
        }
    
        if ( empty( $content ) ) {
          return $old_content;
        }
    
        return $content;
      }
      add_filter('the_content', 'wdss_alt_autocomplete');
    }


    // AMP Template Fix
    if( function_exists('amp_bootstrap_plugin') && get_option('wdss_amp_fix', '0') ) {
      add_filter( 'amp_post_template_data', function( $data, $post ) {
        if ( has_post_thumbnail( $post ) ) {
            $data['featured_image']['amp_html'] = '<amp-img
            src="'.wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium')[0].'"
            width="'.wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium')[1].'"
            height="'.wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium')[2].'"
            class="img-responsive amp-wp-enforced-sizes i-amphtml-element i-amphtml-layout-intrinsic i-amphtml-layout-size-defined i-amphtml-layout"
            alt="featured image"
            layout="responsive"></amp-img>'; 
        }
        return $data;
      }, 10, 2 );

      add_action( 'amp_post_template_css', function() {
        ?>
        @media screen and (max-width: 360px) {
            .amp-wp-article img { display: none;}
        }
        .wp-block-image { width: fit-content; margin: 0 auto; margin-bottom: 1rem; }
        .amp-wp-article-featured-image amp-img { width: 300px; }
        <?php
      } );
    }


    // Disable Automatic Plugins and Theme Updates Snippet
    if( get_option('wdss_disable_autoupdates', '0') ) {
      add_filter( 'auto_update_plugin', '__return_false' );
      add_filter( 'auto_update_theme', '__return_false' );
    }


    // Disable Admin Notices
    if( get_option('wdss_disable_admin_notices', '0') ) {

      function wdss_disable_admin_notices() {   
        // Hide Update notifications
        echo 
        '<style>
            body.wp-admin .update-plugins, 
            body.wp-admin  .plugin-update-tr,
            body.wp-admin #wp-admin-bar-updates,
            body.wp-admin .update-nag,
            .jitm-banner {display: none !important;} 
        </style>';
                      

        // Hide notices from the wordpress backend
        echo 
        '<style> 
          body.wp-admin .error:not(.is-dismissible),
          #yoast-indexation-warning, #akismet_setup_prompt, .jp-wpcom-connect__container {display: none !important;}
          body.wp-admin #loco-content .notice,
          body.wp-admin #loco-notices .notice{display:block !important;}
        </style>';

        // Hide PHP Updates from the wordpress backend
        echo 
        '<style>
          #dashboard_php_nag {display:none;}
        </style>';
                        
      }
      add_action('admin_enqueue_scripts', 'wdss_disable_admin_notices');
      add_action('login_enqueue_scripts', 'wdss_disable_admin_notices');
    }

    
    // Remove redundant links Snippet
    if( get_option( 'wdss_redundant_links', '0' ) ) {
      remove_action( 'wp_head', 'wlwmanifest_link' );
      remove_action( 'wp_head', 'index_rel_link' ); 
      remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); 
      remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); 
      remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); 
      remove_action( 'wp_head', 'wp_generator' );
    
      remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
      remove_action( 'wp_head', 'wp_oembed_add_host_js' );
      remove_action( 'rest_api_init', 'wp_oembed_register_route' );
      remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
      remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

      remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
  
      add_filter( 'embed_oembed_discover', '__return_false' );

      add_filter( 'json_enabled', '__return_false' );
      add_filter( 'json_jsonp_enabled', '__return_false' );
    
      add_filter( 'rest_enabled', '__return_false' );
      add_filter( 'rest_jsonp_enabled', '__return_false' );
    }
  
    
    // Remove Yoast Schema Snippet
    if( function_exists('wpseo_init') && get_option('wdss_yoast_schema', '0') ) {
      add_filter( 'wpseo_json_ld_output', '__return_false' );
    }


    // Set title`s length of posts/pages
    if( function_exists('wpseo_init') && get_option('wdss_enable_title_clipping', '0') ) {
      add_filter('wpseo_title', 'yoast_trim_title');
      function yoast_trim_title($post_title) {
        global $post;
        $is_old_post = false;
        
        if( get_option('wdss_title_clipping_condition', '0') ) {
          $post_date = strtotime($post->post_date);
          
          if( get_option('wdss_title_clipping_by_date', '0') ) {
            $post_date_limiter = strtotime(get_option('wdss_title_clipping_by_date', '0'));
            $is_old_post =  $post_date > $post_date_limiter ? false : true;           
          }
        }

        $words_limit = get_option('wdss_title_words_limit', '6'); // max title`s words amount 
        $symbols_limit = get_option('wdss_word_chars_limit', '35'); // max title`s symbols amount 
        $ending = ' ' . get_option('wdss_title_ending') . ''; // adds title`s ending
        $ending_exists = boolval(strpos($post_title, $ending));
        $title_less_than_count = boolval(mb_strlen($post_title) < $symbols_limit);

        $is_already_exists = $title_less_than_count && $ending_exists;

        if( get_option('wdss_title_clipping_excluded', '') !== '' ) {
          $excludedArr =  explode(',', get_option('wdss_title_clipping_excluded'));
          $is_excluded = in_array($post->ID, $excludedArr);
        }
        
        if ( !is_single()  || $is_already_exists || $is_old_post ||  $is_excluded )   { // where`s title trimming shouldn`t be implemented
          return $post_title;
        }	
        
        $words = explode(' ', $post_title);
        array_splice($words, $words_limit);
        $post_title = implode(' ', $words);

        return $post_title . $ending;
      }
    }


    // Autoptimize Lazyload Fix Snippet
    if( function_exists('autoptimize') && get_option('wdss_autoptimize_lazy_fix', '0') ) {
      add_filter( 'autoptimize_filter_imgopt_lazyload_cssoutput', '__return_false' );
      add_action( 'wp_head', function() { echo '<style>.lazyload,.lazyloading{opacity:0;}.lazyloaded{opacity:1;transition:opacity 300ms;}</style>'; }, 999 );
    }


    // Remove Gutenberg-related stylesheets Snippet
    if( get_option('wdss_gutenberg_styles', '0') ) {
      add_action('wp_enqueue_scripts', 'remove_gutenbers_css', 100);
      function remove_gutenbers_css() {
        wp_dequeue_style('wp-block-library'); 
        wp_dequeue_style('wp-block-library-theme');
      }
    }


    // Redirects Rules Snippet
    if( get_option('wdss_amp_rules', '0') ) {
      function wdss_amp_plugin_change_behavior($url, $status) {
        if ( !is_admin() ) {
  
            if ($_SERVER['SCRIPT_NAME'] === '/wp-login.php'
                || $_SERVER['SCRIPT_NAME'] === '/wp-admin/index.php'
                || $_SERVER['SCRIPT_NAME'] === '/wp-comments-post.php') {
                return $url;
            }
  
            if ($status === 302) {
                wp_redirect(site_url() . $_SERVER['REDIRECT_URL'], 301);
                exit();
            }
  
            if ($status === 301) {
                $regex = '/\/{0,5}([_\?\/])amp[_]*\/?/';
                preg_match($regex, $url, $matches);
                if (!empty($matches)) {
  
                    if (is_front_page() or is_home()) {
                        wp_redirect(site_url(), 301);
                        exit();
                    }
  
                    if (is_category() or is_archive()) {
                        $newUri = preg_replace($regex, '', $_SERVER['REQUEST_URI']);
                        wp_redirect(site_url() . trailingslashit($newUri), 301);
                        exit();
                    }
  
                }
            }
        }
        return $url;
      }
      add_filter('wp_redirect', 'wdss_amp_plugin_change_behavior', 10, 2);
  
      function wdss_generate_rewrite_rules() {
          if ( !is_admin() ) {
              $url = $_SERVER['REQUEST_URI'];
              $regex = '/\/{0,5}([_\?\/])amp[_]*\/?/';
              preg_match($regex, $url, $matches);
              if (!empty($matches)) {
                  if (!is_singular()) {
                      $newUri = preg_replace($regex, '', $_SERVER['REQUEST_URI']);
                      $newUri = trailingslashit($newUri);
                      if (substr($newUri, 0, 1) !== '/') {
                          $newUri = '/' . $newUri;
                      }
                      wp_redirect(site_url() . trailingslashit($newUri), 301);
                      exit();
                  }
  
                  if (is_singular() && !is_category() && !is_archive() && !is_page()) {
                      if (substr($url, -5) !== '/amp/') {
                          $current_url = preg_replace($regex, '/', $url);
                          $current_url .= 'amp/';
                          wp_redirect(site_url($current_url), 301);
                          exit();
                      }
                  }
              }
          }
      }
      add_action('wp', 'wdss_generate_rewrite_rules', -1);
  

      if( !is_admin() ) {
        $url = $_SERVER['REQUEST_URI'];

        if (
          $url == "/index.html"  || 
          $url == "/index.php"   || 
          $url == "/feed"        || 
          $url == "/feed/"       || 
          $url == "/amp"         || 
          $url == "/?amp"        || 
          $url == "/?amp/"       || 
          $url == "//?amp/"
        ) {
          wp_redirect(site_url('/'), 301);
          exit();
        }
      }
    }


    // Force tralling slash
    if( get_option('wdss_forced_trail_slash', '0') ) {

      function wdss_forced_trail_slash($rules) {
          $rule  = "\n";
          $rule .= "# Force Tralling Slash.\n";
          $rule .= "<IfModule mod_rewrite.c>\n";
          $rule .= "RewriteEngine On\n";
          $rule .= "RewriteCond %{REQUEST_URI} !(/$|\.)\n";
          $rule .= "RewriteRule (.*) %{REQUEST_URI}/ [R=301,L]\n";
          $rule .= "</IfModule>\n";
          $rule .= "\n";
        
          return $rule . $rules;
        }

        add_filter('mod_rewrite_rules', 'wdss_forced_trail_slash'); 
    }


    // Disable Feeds
    if( get_option('wdss_disable_rss', '0') )  {

      function remove_feed_links()
      {
        remove_action( 'wp_head', 'feed_links', 2 ); 
        remove_action( 'wp_head', 'feed_links_extra', 3 );       
        remove_action( 'wp_head', 'rsd_link', 4 ); 
      }

      function disabled_feed_behaviour()
      {
        global $wp_rewrite, $wp_query;

          if( isset($_GET['feed']) ) {
            wp_redirect(esc_url_raw(remove_query_arg('feed')), 301);
            exit;
          }

          if( 'old' !== get_query_var('feed') ) {    // WP redirects these anyway, and removing the query var will confuse it thoroughly
            set_query_var('feed', '');
          }

          redirect_canonical();    // Let WP figure out the appropriate redirect URL.

          // Still here? redirect_canonical failed to redirect, probably because of a filter. Try the hard way.
          $struct = (!is_singular() && is_comment_feed()) ? $wp_rewrite->get_comment_feed_permastruct() : $wp_rewrite->get_feed_permastruct();

          $struct = preg_quote($struct, '#');
          $struct = str_replace('%feed%', '(\w+)?', $struct);
          $struct = preg_replace('#/+#', '/', $struct);
          $requested_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

          $new_url = preg_replace('#' . $struct . '/?$#', '', $requested_url);

          if( $new_url !== $requested_url ) {
            wp_redirect($new_url, 301);
            exit;
          }
      }    
      
      function filter_feeds()
      {
        if( !is_feed() || is_404() ) {
          return;
        }

        disabled_feed_behaviour();
      }

      add_action('wp_loaded', 'remove_feed_links');
      add_action('template_redirect', 'filter_feeds', 1);

    }


    // Yoast Canonical Pagination Fix Snippet
    if( get_option('wdss_yoast_canonical_fix', '0') ) {
      add_filter('wpseo_canonical', 'wdss_fix_canon_pagination');
      function wdss_fix_canon_pagination($link) {
        $link = preg_replace('#\??/page[\/=]\d+#', '', $link);
        return $link;
      }
    }

    
    // 410 Category Rules
    if( get_option('wdss_410_rules', '0') ) {
      function wdss_force_410()
      {
          $requestUri = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
          $requestUri = urldecode($requestUri);
          switch ($requestUri) {
              case 'https://'.$_SERVER['HTTP_HOST'].'/uncategorized/':
              case 'https://'.$_SERVER['HTTP_HOST'].'/uncategorized':
              case 'https://'.$_SERVER['HTTP_HOST'].'/bez-kategorii/':
              case 'https://'.$_SERVER['HTTP_HOST'].'/bez-kategorii':
              case 'https://'.$_SERVER['HTTP_HOST'].'/без-категории/':
              case 'https://'.$_SERVER['HTTP_HOST'].'/без-категории':
                  global $post, $wp_query;
                  $wp_query->set_404();
                  status_header(404);
                  header("HTTP/1.0 410 Gone");
                  include(get_query_template('404'));
                  exit;
          }
      }
      add_action( 'wp', 'wdss_force_410' );
    }
  }


  function wdss_admin_menu() {
    add_menu_page('WD Sattelites Snippets', 'WD Sattelites Snippets', 'manage_options', 'wd-sattelites-snippets', array($this, 'wdss_settings_template'), 'dashicons-admin-tools', 100 );
  }

  function checkbox_handler_html($args) { ?>
    <input type="checkbox" name="<?= $args['field_name']; ?>" value="1" <?php checked(esc_attr(get_option($args['field_name']))) ?>>  
  <? }

  function text_handler_html($args) { ?>
    <input type="text" name="<?= $args['field_name']; ?>" value="<?= esc_attr(get_option($args['field_name']));?>" >  
  <? }


  function date_handler_html($args) { ?>
    <input type="date" name="<?= $args['field_name']; ?>" value="<?= esc_attr(get_option($args['field_name']));?>" >  
  <? }


  function number_handler_html($args) { ?>
    <input type="number" name="<?= $args['field_name']; ?>" min="<?= $args['min']; ?>" max="<?= $args['max']; ?>" value="<?= esc_attr(get_option($args['field_name']));?>" >  
  <? }


  function wdss_form_handler() {
    if(wp_verify_nonce($_POST['wfp_nonce'], 'wdss_save_settings') && current_user_can('manage_options')) { 

      update_option('wdss_last_modified_n_304', sanitize_text_field($_POST['wdss_last_modified_n_304'])); 
      update_option('wdss_forced_trail_slash', sanitize_text_field($_POST['wdss_forced_trail_slash']));    
      
      update_option('wdss_redundant_links', sanitize_text_field($_POST['wdss_redundant_links']));   

      update_option('wdss_disable_autoupdates', sanitize_text_field($_POST['wdss_disable_autoupdates']));
      update_option('wdss_disable_admin_notices', sanitize_text_field( $_POST['wdss_disable_admin_notices']));
      update_option('wdss_disable_rss', sanitize_text_field( $_POST['wdss_disable_rss']));      
      
      update_option('wdss_auto_featured_image', sanitize_text_field( $_POST['wdss_auto_featured_image']));
      update_option('wdss_auto_alt_attribute', sanitize_text_field( $_POST['wdss_auto_alt_attribute']));

      update_option('wdss_yoast_schema', sanitize_text_field($_POST['wdss_yoast_schema']));   
      update_option('wdss_yoast_canonical_fix', sanitize_text_field($_POST['wdss_yoast_canonical_fix']));   
      update_option('wdss_autoptimize_lazy_fix', sanitize_text_field($_POST['wdss_autoptimize_lazy_fix']));   
      update_option('wdss_gutenberg_styles', sanitize_text_field($_POST['wdss_gutenberg_styles']));   
      update_option('wdss_amp_rules', sanitize_text_field($_POST['wdss_amp_rules']));  
      update_option('wdss_amp_fix', sanitize_text_field($_POST['wdss_amp_fix']));   
      update_option('wdss_force_lowercase', sanitize_text_field($_POST['wdss_force_lowercase']));  
      
      update_option('wdss_enable_title_clipping', sanitize_text_field($_POST['wdss_enable_title_clipping']));   
      update_option('wdss_title_clipping_excluded', sanitize_text_field($_POST['wdss_title_clipping_excluded']));
      update_option('wdss_title_clipping_condition', sanitize_text_field($_POST['wdss_title_clipping_condition']));   
      update_option('wdss_title_clipping_by_date', sanitize_text_field($_POST['wdss_title_clipping_by_date']));         
      

      update_option('wdss_title_words_limit', sanitize_text_field($_POST['wdss_title_words_limit']));  
      update_option('wdss_word_chars_limit', sanitize_text_field($_POST['wdss_word_chars_limit']));  
      update_option('wdss_title_ending', sanitize_text_field($_POST['wdss_title_ending']));  
      
      update_option('wdss_410_rules', sanitize_text_field($_POST['wdss_410_rules']));

    ?>

      <div class="notice updated">
        <p><?= __('Settings are updated', 'wdss_domain') ?></p>
      </div>
    <?php }
      else { ?>
      <div class="notice error">
        <p><?= __('You don`t have permissions to do this', 'wdss_domain') ?></p>
      </div>
    <?php  }
  }


  function wdss_settings_template() { ?>
    <div id="wdss-settings-page">
      <div class="container">

        <h1>Satellites Snippets <small>ver <?= WDSS_VERSION ?></small></h1>

        <?php $_POST['wdss_form_submitted'] === 'true' ? $this->wdss_form_handler() : null; ?>

        <form method="POST">

          <input type="hidden" name="wdss_form_submitted" value='true'>
          <?php wp_nonce_field('wdss_save_settings', 'wfp_nonce'); ?>


          <section id="wdss-snippets-settings">
              <h2>Snippets List</h2>
              <div class="wdss-row">
                  <div id="wdss-last-modified" class="wdss-setting-item">
                      <label>
                        <span>Last Modified and 304</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_last_modified_n_304']); 
                          if( get_option('wdss_last_modified_n_304') == '' ) update_option( 'wdss_last_modified_n_304', '0' );               
                        ?>    
                    </label>
                  </div>
                
                  <div id="wdss-last-modified" class="wdss-setting-item">
                      <label>
                        <span title="To apply changes visit Permalinks page ">Force Trailling Slash *</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_forced_trail_slash']); 
                          if( get_option('wdss_forced_trail_slash') == '' ) update_option( 'wdss_forced_trail_slash', '0' );               
                        ?>    
                    </label>
                  </div>

                  <div id="wdss-redundant-links" class="wdss-setting-item">
                      <label>
                        <span>Remove Redundant Links</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_redundant_links']); 
                          if( get_option('wdss_redundant_links') == '' ) update_option( 'wdss_redundant_links', '0' );               
                        ?>    
                    </label>
                  </div>

                  <div id="wdss-auto-featured-image" class="wdss-setting-item">
                      <label>
                        <span>Auto Featured Image</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_auto_featured_image']); 
                          if( get_option('wdss_auto_featured_image') == '' ) update_option( 'wdss_auto_featured_image', '0' );               
                        ?>    
                    </label>
                  </div>

                  <div id="wdss-auto-alt-attr" class="wdss-setting-item">
                      <label>
                        <span>Auto Alt Attributes</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_auto_alt_attribute']); 
                          if( get_option('wdss_auto_alt_attribute') == '' ) update_option( 'wdss_auto_alt_attribute', '0' );               
                        ?>    
                    </label>
                  </div>


                  
                  <div id="wdss-disable-rss" class="wdss-setting-item">
                      <label>
                        <span>Disable RSS Feeds</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_disable_rss']); 
                          if( get_option('wdss_disable_rss') == '' ) update_option( 'wdss_disable_rss', '0' );               
                        ?>    
                    </label>
                  </div>

                  <div id="wdss-disable-autoupdates" class="wdss-setting-item">
                      <label>
                        <span>Disable Auto Updates</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_disable_autoupdates']); 
                          if( get_option('wdss_disable_autoupdates') == '' ) update_option( 'wdss_disable_autoupdates', '0' );               
                        ?>    
                    </label>
                  </div>

                  <div id="wdss-disable-admin-notices" class="wdss-setting-item">
                      <label>
                        <span>Disable Admin Notices</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_disable_admin_notices']); 
                          if( get_option('wdss_disable_admin_notices') == '' ) update_option( 'wdss_disable_admin_notices', '0' );               
                        ?>    
                    </label>
                  </div>                  

                  <?php if( function_exists('wpseo_init') ) { ?>    
                  <div id="wdss-yoast-schema" class="wdss-setting-item">
                      <label>
                        <span>Remove Yoast Schema</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_yoast_schema']); 
                          if( get_option('wdss_yoast_schema') == '' ) update_option( 'wdss_yoast_schema', '0' );               
                        ?>    
                    </label>
                  </div>      
                  <?php } ?>         

                  <?php if( function_exists('wpseo_init') ) { ?>    
                  <div id="wdss-yoast-canonic-pager" class="wdss-setting-item">
                      <label>
                        <span>Yoast Canonical Pagination Fix</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_yoast_canonical_fix']); 
                          if( get_option('wdss_yoast_canonical_fix') == '' ) update_option( 'wdss_yoast_canonical_fix', '0' );               
                        ?>    
                    </label>
                  </div>
                  <?php } ?>   

                  <?php if( function_exists('autoptimize') ) { ?>           
                  <div id="wdss-autoptimize-lazyload" class="wdss-setting-item">
                      <label>
                        <span>Autoptimize Lazyload Fix</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_autoptimize_lazy_fix']); 
                          if( get_option('wdss_autoptimize_lazy_fix') == '' ) update_option( 'wdss_autoptimize_lazy_fix', '0' );               
                        ?>    
                    </label>
                  </div>
                  <?php } ?>

                  <div id="wdss-gutenberg-styles" class="wdss-setting-item">
                      <label>
                        <span>Remove Gutenberg stylesheets</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_gutenberg_styles']); 
                          if( get_option('wdss_gutenberg_styles') == '' ) update_option( 'wdss_gutenberg_styles', '0' );               
                        ?>    
                    </label>
                  </div> 

                  <div id="wdss-redundant-links" class="wdss-setting-item">
                      <label>
                        <span>Basic Redirect Rules</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_amp_rules']); 
                          if( get_option('wdss_amp_rules') == '' ) update_option( 'wdss_amp_rules', '0' );               
                        ?>    
                    </label>
                  </div>

                  <?php if( function_exists('amp_bootstrap_plugin') ) { ?>
                  <div id="wdss-amp-template-fix" class="wdss-setting-item">
                      <label>
                        <span>AMP Template Fix</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_amp_fix']); 
                          if( get_option('wdss_amp_fix') == '' ) update_option( 'wdss_amp_fix', '0' );               
                        ?>    
                    </label>
                  </div>
                  <?php } ?>


                <div class="wdss-col-4">
                  <div id="wdss-lowercase-urls" class="wdss-setting-item">
                      <label>
                        <span>Force Lowercase URLs</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_force_lowercase']); 
                          if( get_option('wdss_force_lowercase') == '' ) update_option( 'wdss_force_lowercase', '0' );               
                        ?>    
                    </label>
                  </div> 
              </div>
          </section>

          <section id="wdss-title-clipping-settings">
            <h2>Long Title Clipping Settings <small>(posts only)</small></h2>
            <div class="wdss-row">
                <div id="wdss-title-clipping" class="wdss-setting-item">
                    <label>
                      <span>Enable</span>
                      <?php 
                        $this->checkbox_handler_html(['field_name' => 'wdss_enable_title_clipping']); 
                        if( get_option('wdss_enable_title_clipping') == '' ) update_option( 'wdss_enable_title_clipping', '0' );               
                      ?>    
                  </label>
                </div>    

                <div id="wdss-title-clipping-group" class="wdss-setting-group">

                  <div id="wdss-title-clipping-condition-group">
                    <div id="wdss-title-clipping-condition" class="wdss-setting-item">
                    <label>
                          <span>Date Condition </span>
                          <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_title_clipping_condition']); 
                          if( get_option('wdss_title_clipping_condition') == '' ) update_option( 'wdss_title_clipping_condition', '0' );               
                          ?>     
                      </label>
                    </div> 
                    
                    
                    <div id="wdss-title-clipping-by-date" class="wdss-setting-item wdss-setting-group">
                    <label>
                          <span>-- Cut titles since</span>
                          <?php 
                          $this->date_handler_html(['field_name' => 'wdss_title_clipping_by_date']); 
                          if( get_option('wdss_title_clipping_by_date') == '' ) update_option( 'wdss_title_clipping_by_date', '0' );               
                          ?>  

                      </label>
                    </div> 

                  </div>


                  <div id="wdss-words-limit" class="wdss-setting-item">
                  <label>
                        <span title="by default: 6">Words Limit</span>
                        <?php 
                          $default_words_limit = '6';
                          $this->number_handler_html(['field_name' => 'wdss_title_words_limit', 'min' => '2', 'max' => '7']); 
                          if( get_option('wdss_title_words_limit') == '' ) update_option( 'wdss_title_words_limit', $default_words_limit );               
                        ?>    
                    </label>
                  </div>   

                  <div id="wdss-chars-limit" class="wdss-setting-item">
                      <label>
                        <span title="by default: 35">Chars Per Word</span>
                        <?php 
                          $default_chars_limit = '35';
                          $this->number_handler_html(['field_name' => 'wdss_word_chars_limit', 'min' => '32', 'max' => '50']); 
                          if( get_option('wdss_word_chars_limit') == '' ) update_option( 'wdss_word_chars_limit', $default_chars_limit );               
                        ?>    
                    </label>
                  </div>   

                  <div id="wdss-title-clipping-excluded" class="wdss-setting-item">
                      <label>
                        <span title="comma separated">Exclude by ID</span>
                        <?php 
                          $this->text_handler_html(['field_name' => 'wdss_title_clipping_excluded']); 
                          if( get_option('wdss_title_clipping_excluded') == '' ) update_option( 'wdss_title_clipping_excluded', '' );               
                        ?>    
                    </label>
                  </div>  


                  


                  
                  <div id="wdss-title-ending" class="wdss-setting-item">
                      <label>
                        <span>Title Ending (with divider)</span>
                        <?php 
                          $this->text_handler_html(['field_name' => 'wdss_title_ending']); 
                          if( get_option('wdss_title_ending') == '' ) update_option( 'wdss_title_ending', '' );               
                        ?>    
                    </label>
                  </div> 
                </div> 
            </div>
          </section>


          <?php 
            $total_post_count = wp_count_posts('post')->publish;
            if( $total_post_count <= 1 ) { ?>
              <section id="wdss-410-settings">
                <h2>410 Status for Category Pages</h2>
                <div class="wdss-row">
                  <div id="wdss-category-410" class="wdss-setting-item">
                    <label>
                      <span>Enable</span>
                      <?php $this->checkbox_handler_html(['field_name' => 'wdss_410_rules']); ?>
                    </label>
                  </div>
                </div>
              </section>
            <?php }
            else {
              update_option('wdss_410_rules', '0');
            }        
          ?>

          <input type="submit" name="submit" id="submit" class="button" value="<?= __('Save changes', 'wdss_domain') ?>">
        </form>
      </div>
    </div>
  <?php }
}


$wd_Sattelites_Snippets = new WD_Sattelites_Snippets();