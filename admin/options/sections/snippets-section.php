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


    // Sets correct redirect link on comment submit
    add_filter( 'comment_post_redirect', 'wdss_redirect_comments', 10, 2 );
    function wdss_redirect_comments( $location, $commentdata ) {
      if(!isset($commentdata) || empty($commentdata->comment_post_ID) ){
        return $location;
      }
      $post_id = $commentdata->comment_post_ID;
      return $location;
    }

    
    // Removes Website field from comment form
    add_filter('comment_form_default_fields', 'wdss_unset_url_field');
    function wdss_unset_url_field($fields){
      if(isset($fields['url']))
        unset($fields['url']);
        return $fields;
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
    function wdss_alt_attachment_autocomplete($html, $post_id, $post_thumbnail_id, $size, $attr) {
        
        if(!isset($attr['alt'])) {
          $id = get_post_thumbnail_id();
          $alt_text = get_the_title($post_id);
       
          $html = str_replace( 'alt=""', 'alt="' . esc_attr( $alt_text ) . '"', $html );
        }

        return $html;
    }
    add_filter('post_thumbnail_html', 'wdss_alt_attachment_autocomplete', 10, 5);      


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
		
      // Remove Images From Content Helper
      function remove_images_from_content($content) {
        $content = preg_replace("/<img[^>]+>/i", "", $content);
        return $content;
      }
      
      // Remove standard layout and paste custom one
      add_filter( 'amp_post_article_header_meta', function ( $meta_parts ) {
        $meta_parts = [];
  
        global $post;
        $category = get_the_category();
        $first_category = $category[0];
  
        echo '<div class="amp-wp-post-info"><span>' . get_the_date('d.m.Y') . '</span>' . 
        sprintf( '<a href="%s">%s</a>', get_category_link( $first_category ), $first_category->name ) . '</div>';
  
        return $meta_parts;
      } );
  
      // Filters Template Parts
      add_filter( 'amp_post_template_data', function( $data, $post ) {
          // Remove comments links
          add_filter( 'amp_post_article_footer_meta', function ( $meta_parts ) {
              $meta_parts = [];
              return $meta_parts;
          } );
  
          // Removes favicon
          $data['site_icon_url'] = '';
  
          // Removes Featured Image
        if ( has_post_thumbnail( $post ) ) {
          $data['featured_image']['amp_html'] = ''; 
          $data['featured_image']['caption'] = ''; 
        }
  
          // Removes all images from content
          $data['post_amp_content'] = remove_images_from_content($data['post_amp_content']);
  
        return $data;
      }, 999, 2 );
  
  
      // Sets custom css for template
      add_action( 'amp_post_template_css', function() {
      ?>
        .back-to-top { display: none;}
      .wp-block-image { width: fit-content; margin: 0 auto; margin-bottom: 1rem; }
        .amp-site-title { display: block; text-align: center; font-size: 1.5rem; }
        .amp-wp-post-info span { display:block; }
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

        // If last modify is enabled then hide Delete Cache button
        if(get_option('wdss_last_modified_n_304', '0')) { 
          echo 
          '<style>#wp-admin-bar-delete-cache, #wp-admin-bar-autoptimize-delete-cache {display: none !important;}</style>';
        }

        // Hide Update notifications
        echo 
        '<style>
            body.wp-admin .update-plugins, 
            body.wp-admin  .plugin-update-tr,
            body.wp-admin #wp-admin-bar-updates,
            body.wp-admin .update-nag,
            .jitm-banner {display: none !important;} 
            
            #wd-satellites-snippets-update, #wd-bulk-validation-fixer-update {display: table-row !important;} 
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
      if( !is_admin() ) {
        add_action('wp', 'wdss_remove_redundant_links');
        function wdss_remove_redundant_links() {
          remove_action( 'wp_head', 'wlwmanifest_link' );
          remove_action( 'wp_head', 'index_rel_link' ); 
          remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); 
          remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); 
          remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); 
          remove_action( 'wp_head', 'wp_generator' );
          remove_action( 'wp_head', 'wp_resource_hints', 2, 99 ); 
    
          wp_dequeue_script( 'wp-embed' );

          remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
          remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
          remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
          remove_action( 'template_redirect', 'rest_output_link_header', 11 );
    
          remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
          remove_action( 'wp_head', 'wp_oembed_add_host_js' );
          remove_action( 'rest_api_init', 'wp_oembed_register_route' );
          remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
          remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );

          remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
          add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
          add_filter( 'embed_oembed_discover', '__return_false' );
    
          add_filter( 'json_enabled', '__return_false' );
          add_filter( 'json_jsonp_enabled', '__return_false' );
        
          add_filter( 'rest_enabled', '__return_false' );
          add_filter( 'rest_jsonp_enabled', '__return_false' );

          remove_action('wp_head', 'rsd_link');
          add_filter( 'xmlrpc_enabled', '__return_false' );
          remove_action('wp_head', 'adjacent_post_rel_link_wp_head', 10, 0);
          
          remove_action('wp_head', 'print_emoji_detection_script', 7);
          remove_action('wp_print_styles', 'print_emoji_styles');
        }
      }
    }      


    // Disable Gutenberg Block Editor & New Block Widget Area
    if(get_option('wdss_disable_gutenberg', '0')) {
       // Disables the block editor itself
      add_filter( 'use_block_editor_for_post', '__return_false' );
      // Disables the block editor from managing widgets in the Gutenberg plugin.
      add_filter( 'gutenberg_use_widgets_block_editor', '__return_false', 100 );
      // Disables the block editor from managing widgets. renamed from wp_use_widgets_block_editor
      add_filter( 'use_widgets_block_editor', '__return_false' );
    }


    // Additional option to allow *data* protocol(?) on images
    add_filter('kses_allowed_protocols', function ($protocols) {
      $protocols[] = 'data';
      return $protocols;
    });

    // Disable X-Pingback to header
    add_filter( 'wp_headers', 'disable_x_pingback' );
    function disable_x_pingback( $headers ) {
      unset( $headers['X-Pingback'] );
      return $headers;
    }    

    // Sets X Default Hreflang`s 
    if(function_exists('pll_languages_list')) {
      function wdss_x_default_hreflang() {
        $languages = icl_get_languages('skip_missing=1');
        foreach($languages as $l){
            if ($l['language_code'] == 'pl' ) {
                $x_default_url = $l['url'];
          if(is_paged()){
            $output='<link rel="alternate" href="' . $x_default_url . '" />'  . PHP_EOL;
          }else{
            $output='<link rel="alternate" hreflang="x-default" href="' . $x_default_url . '" />'  . PHP_EOL;
          }
                echo $output;
            }
        }
      }
      add_action('wp_head','wdss_x_default_hreflang');
    }



    //Lazy load for iframes
    if( get_option('lazy_load_for_iframes', '0') ) {

      add_action('wp_enqueue_scripts', 'wdss_lazy_iframe_enqueue');
      function wdss_lazy_iframe_enqueue() {
        wp_enqueue_script('iframe-lazy', plugin_dir_url( __FILE__ ) . '../inc/iframe-lazy/index.js', array(), '1.0', true);
        wp_enqueue_style('iframe-lazy', plugin_dir_url( __FILE__ ) . '../inc/iframe-lazy/index.css', array(), '1.0');
      }

      add_filter( 'the_content', 'wdss_filter_the_content_in_the_main_loop', 1 );
      function wdss_filter_the_content_in_the_main_loop( $string ) {
      
        $regex = "/<(iframe)(.*?)>(<\/(iframe)>)?/i";
        preg_match_all($regex, $string, $matches);
      
        $regex_new = '/src="([^"]*)"/';
        $regex_url_new = '/(.*?)\?/i';

        // Checks if iframe neither broken nor Google Maps
        if (!empty($matches[2]) && !strpos($matches[2][0], 'maps') ) {
          foreach ($matches[2] as $key => $elem) {
            preg_match_all($regex_new, $matches[0][$key], $matches_url);
            $img_link_array = explode('/',$matches_url[1][0]);
            $img_link = end($img_link_array);
            preg_match_all($regex_url_new, $img_link, $matches_url_new);
      
            if (isset($matches_url_new[1][0]) && !empty($matches_url_new[1][0])) {
              $new_img_link = "https://img.youtube.com/vi/". $matches_url_new[1][0] ."/sddefault.jpg";
              if (check_url_status($new_img_link) && !file_get_contents( $new_img_link)) {
                $new_img_link = "https://i.ytimg.com/vi_webp/". $matches_url_new[1][0] ."/hqdefault.webp";
              }
            } else {
              $new_img_link = "https://img.youtube.com/vi/". $img_link ."/sddefault.jpg";
              if (check_url_status($new_img_link) && !file_get_contents( $new_img_link)) {
                $new_img_link = "https://i.ytimg.com/vi_webp/". $img_link ."/hqdefault.webp";
              }
            }
      
            $newAttributes = preg_replace('/allowfullscreen(=".*?")?/s','',$elem);
            $newAttributes = preg_replace('/(\w+)\=((\'|").*?(\'|"))/i', "data-$1=$2", $newAttributes);
            $newAttributes = $newAttributes . ' data-img-src="'.$new_img_link.'"';
            $string = str_replace($matches[0][$key], "<div{$newAttributes} class='lazy-embed inactive'></div>", $string);
          }
        }
        return $string;
      }  
    }

    // Disable the emoji's
    if( get_option( 'wdss_disable_emojis', '0' ) ) {
      function wdss_disable_emojis() {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        add_filter( 'tiny_mce_plugins', 'wdss_disable_emojis_tinymce' );
        add_filter( 'wp_resource_hints', 'wdss_disable_emojis_remove_dns_prefetch', 10, 2 );
      }
      add_action( 'wp', 'wdss_disable_emojis' );
      
      // Filter function used to remove the tinymce emoji plugin.
      function wdss_disable_emojis_tinymce( $plugins ) {
        if ( is_array( $plugins ) ) {
          return array_diff( $plugins, array( 'wpemoji' ) );
        } else {
          return array();
        }
      }
      
      function wdss_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
        if ( 'dns-prefetch' == $relation_type ) {
          /** This filter is documented in wp-includes/formatting.php */
          $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
      
          $urls = array_diff( $urls, array( $emoji_svg_url ) );
        }
      
      return $urls;
      }
    }

    // Remove Yoast Schema Snippet
    if( function_exists('wpseo_init') && get_option('wdss_yoast_schema', '0') ) {
      add_filter( 'wpseo_json_ld_output', '__return_false' );
    }


    // Removes homepage pagination
    if( get_option('wdss_remove_homepage_pagination', '0') ) {
      function wdss_remove_homepage_pagination() {
        global $post, $wp_query;
        if ((is_home() || is_front_page()) && (is_paged())) {
        $wp_query->set_404();
        status_header(404);
        include( get_query_template( '404' ) );
        exit;
        }
      }
      add_action( 'template_redirect', 'wdss_remove_homepage_pagination', 1 );
    }


    // 410 Category Rules
    function wdss_force_410() {
        $requestUri = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $requestUri = urldecode($requestUri);
        switch ($requestUri) {
            case 'https://'.$_SERVER['HTTP_HOST'].'/uncategorized/':
            case 'https://'.$_SERVER['HTTP_HOST'].'/uncategorized':
            case 'https://'.$_SERVER['HTTP_HOST'].'/bez-kategorii/':
            case 'https://'.$_SERVER['HTTP_HOST'].'/bez-kategorii':
            case 'https://'.$_SERVER['HTTP_HOST'].'/без-категории/':
            case 'https://'.$_SERVER['HTTP_HOST'].'/без-категории':
            case 'https://'.$_SERVER['HTTP_HOST'].'/без-рубрики/':
            case 'https://'.$_SERVER['HTTP_HOST'].'/без-рубрики':
                global $post, $wp_query;
                $wp_query->set_404();
                status_header(404);
                header("HTTP/1.0 410 Gone");
                include(get_query_template('404'));
                exit;
        }
    }
    add_action( 'wp', 'wdss_force_410' );


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

        if(function_exists('pll_languages_list')) {
          $langs = count(pll_languages_list());
          if( 
            function_exists('pll_current_language') && $langs > 0 &&
            $url == '/' . pll_current_language() . '/index.php' ||
            $url == '/' . pll_current_language() . '/index.html' 
            ) {
            wp_redirect(site_url('/' . pll_current_language()), 301);
            exit();
          }
        } 
        elseif (
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


    // Disable pingbacks
    if( get_option('wdss_disable_pingbacks', '0') ) {

      add_action( 'pre_ping', 'wdss_no_self_ping' );
      function wdss_no_self_ping( &$links ) {
        $home = get_option( 'home' );
        foreach ( $links as $l => $link ) {
          if ( 0 === strpos( $link, $home ) ) {
            unset($links[$l]);
          }
        }
      }
    }


    // Disable Feeds
    if( get_option('wdss_disable_rss', '0') )  {

      remove_action( 'wp_head', 'feed_links', 2 ); 
      remove_action( 'wp_head', 'feed_links_extra', 3 );       
      remove_action( 'wp_head', 'rsd_link', 4 ); 
      
      function wdss_disabled_feed_behaviour()
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
      
      function wdss_filter_feeds()
      {
        if( !is_feed() || is_404() ) {
          return;
        }

        wdss_disabled_feed_behaviour();
      }

      add_action('template_redirect', 'wdss_filter_feeds', 1);

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
    function wdss_custom_excerpts( $excerpt, $raw_excerpt ) {
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
    add_filter( 'wp_trim_excerpt', 'wdss_custom_excerpts', 99, 2 );
    
    
    // Removes Yoast`s Rel Next link from homepage
    if( get_option('wdss_remove_homepage_pagination', '0') ) {
      add_filter( 'wpseo_next_rel_link', 'wdss_remove_wpseo_next_home' );
      function wdss_remove_wpseo_next_home( $link ) {
          if ( is_front_page() ) {
              $link = '';
          }
          return $link;
      }
    }

    // Fixes bad validation on WP Security Captcha
    add_filter('the_content', 'wdss_wp_security_captcha_fix', 10);
    function wdss_wp_security_captcha_fix($content) {
        if( is_single() ) {
          $content = preg_replace('/<p class="aiowps-captcha">/', '<div class="aiowps-captcha">',  $content);
          $content = preg_replace('/<\/strong><\/div><\/p>/', '</strong><div></div>', $content);
        }
        return $content;
    }

    // Custom Descriptions for imported articles
    function wdss_custom_post_descriptions($meta_description)  {
       
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
    add_filter('wpseo_metadesc', 'wdss_custom_post_descriptions', 10, 2 );
    add_filter('wpseo_opengraph_desc', 'wdss_custom_post_descriptions', 10, 2);