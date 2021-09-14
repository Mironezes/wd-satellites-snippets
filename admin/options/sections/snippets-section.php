<?php

// Disables jQuery and Migration script for Frontend
    if( get_option('wdss_disable_jquery', '0') ) {
      function wdss_disable_jquery() {
        if( !is_admin() ) {
          wp_deregister_script( 'jquery' );
          wp_deregister_script( 'jquery-migrate' );
        }
      }
      add_action('wp_enqueue_scripts', 'wdss_disable_jquery');
    }

		// Force Lowercase URLs Snippet
		if( get_option('wdss_force_lowercase', '0') ) {
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
		}


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


    // Auto Alt Attributes for the rest images
    if( get_option('wdss_auto_alt_attribute', '0') ) {
      function wdss_alt_singlepage_autocomplete( $content ) {
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
      add_filter('the_content', 'wdss_alt_singlepage_autocomplete');

      function wdss_alt_attachment_autocomplete($html, $post_id, $post_thumbnail_id, $size, $attr) {
        
        if(!isset($attr['alt'])) {
          $id = get_post_thumbnail_id();
          $alt_text = get_the_title($post_id);
       
          $html = str_replace( 'alt=""', 'alt="' . esc_attr( $alt_text ) . '"', $html );
        }

        return $html;
      }
      add_filter('post_thumbnail_html', 'wdss_alt_attachment_autocomplete', 10, 5);      
    }


    // Fixes WP Comments Passive Listener Issue 
    if( get_option('wdss_comments_passive_listener_fix', '0') ) {
      if( is_single() ) {      
        function wp_dereg_script_comment_reply(){wp_deregister_script( 'comment-reply' );}
          add_action('init','wp_dereg_script_comment_reply');
          add_action('wp_head', 'wp_reload_script_comment_reply');
        function wp_reload_script_comment_reply() {
        ?>
        <script>
          //Function checks if a given script is already loaded
          function isScriptLoaded(src){
              return document.querySelector('script[src="' + src + '"]') ? true : false;
          }
          //When a reply link is clicked, check if reply-script is loaded. If not, load it and emulate the click
          document.getElementsByClassName("comment-reply-link").onclick = function() { 
              if(!(isScriptLoaded("/wp-includes/js/comment-reply.min.js"))){
                  var script = document.createElement('script');
                  script.src = "/wp-includes/js/comment-reply.min.js"; 
              script.onload = emRepClick($(this).attr('data-commentid'));        
                  document.head.appendChild(script);
              } 
          }
          //Function waits 50 ms before it emulates a click on the relevant reply link now that the reply script is loaded
          function emRepClick(comId) {
          sleep(50).then(() => {
          document.querySelectorAll('[data-commentid="'+comId+'"]')[0].dispatchEvent(new Event('click'));
          });
          }
          //Function does nothing, for a given amount of time
          function sleep (time) {
            return new Promise((resolve) => setTimeout(resolve, time));
          }
        </script>
        <?php
        }  
      }
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


    // Removes WP Hentry markup
    if( get_option('wdss_remove_hentry', '0') ) {
      function wdss_remove_hentry( $classes ) {
        if ( is_home() || is_archive() || is_singular() ) {
          $classes = array_diff( $classes, array( 'hentry' ) );
        }
        return $classes;
      }
      add_filter( 'post_class','wdss_remove_hentry' );
    }
    

    // Remove redundant links Snippet
    if( get_option( 'wdss_redundant_links', '0' ) ) {
      remove_action( 'wp_head', 'wlwmanifest_link' );
      remove_action( 'wp_head', 'index_rel_link' ); 
      remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); 
      remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); 
      remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); 
      remove_action( 'wp_head', 'wp_generator' );
      remove_action( 'wp_head', 'wp_resource_hints', 2, 99 ); 

      remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

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


    // Auto width/height attributes
    if( get_option('wdss_auto_widght_height_attr', '0') ) {
      if( !is_admin()) {

    
        add_filter('the_content', 'set_image_dimension', 20);
        function set_image_dimension($content) {
            if( is_single() ) {
        
                $buffer = $content;
                
                // Get all images without width or height attribute
                $pattern1 = '/<img(?:[^>](?!(height|width)=))*+>/i';
                $pattern2 = '/<img(?:(\s*(height|width)\s*=\s*"([^"]+)"\s*)+|[^>]+?)*>/i';
  
                preg_match_all($pattern1, $content, $first_match );
                preg_match_all($pattern2, $content, $second_match );
                
                $all_images = array_merge($first_match[0], $second_match[0]);
                foreach ( $all_images as $image ) {
                
                $tmp = $image;
            
                // Get link of the file
                preg_match( '/src=[\'"]([^\'"]+)/', $image, $src_match );

                // Get image url
                if( !empty($src_match) ) {

                  // If url contains full address
                  if(!empty(strpos($src_match[0], site_url()))) {
                    $image_url = $src_match[1];
                  }
                  // If url contains relative address then adds domain
                  else {
                    $image_url = site_url().$src_match[1];
                  }

                  if(!mb_strpos($image_url, 'wp-content') ) {
                      continue;
                  }
              
                  // Get image dimension
                  list($width, $height) = wp_getimagesize($image_url );
                  $dimension = 'width="'.$width.'" height="'.$height.'" ';
                  
                  // Add width and width attribute
                  $image = str_replace( '<img', '<img ' . $dimension, $image );
                  
                  // Replace image with new attributes
                  $buffer = str_replace( $tmp, $image, $buffer );
                  }
                }
                return $buffer;
            }
            return $content; 
        }
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

    // Custom Excerpts for imported articles
    function custom_excerpts( $excerpt, $raw_excerpt ) {
      if ( is_admin() ||  '' !== $raw_excerpt) {
        return $excerpt;
      }
     
      $condition = '#<div[^>]*id="toc"[^>]*>.*?</div>#is';
      $content = apply_filters( 'the_content', get_the_content() );
    
      if( preg_match($condition, $content) ) {
        $clear = preg_replace($condition, '', $content);	
        $stripped = strip_tags($clear);
        $excerpt = mb_substr($stripped, 0, 500, 'UTF-8') . ' [...]';
        
        return $excerpt;
      }
    
      return $excerpt;
    
    }
    add_filter( 'wp_trim_excerpt', 'custom_excerpts', 99, 2 );
    
    
    // Custom Descriptions for imported articles
    function custom_post_descriptions($meta_description, $presentation)  {
       
      if( is_single() ) {
        $condition = '#<div[^>]*id="toc"[^>]*>.*?</div>#is';
        $content = apply_filters( 'the_content', get_the_content() );
      
        if( preg_match($condition, $content) ) {
          $raw = apply_filters( 'the_content', get_the_content() );
          $clear = preg_replace('#<div[^>]*id="toc"[^>]*>.*?</div>#is', '', $raw);	
          $stripped = strip_tags($clear);
          $meta_description = mb_substr($stripped, 0, 150, 'UTF-8') . ' [...]';
        }
      }

      return $meta_description;
    
    }
    add_filter('wpseo_metadesc', 'custom_post_descriptions', 10, 2 );
    add_filter('wpseo_opengraph_desc', 'custom_post_descriptions', 10, 2);
    