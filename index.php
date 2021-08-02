<?php 

/*
  Plugin Name: WD Sattelites Snippets
  Plugin URI: https://github.com/Mironezes
  Description: Bulk of usefull snippets for our sattelites 
  Version: 0.2.1
  Author: Alexey
  Author URI: https://github.com/Mironezes
  Text Domain: wdss_domain
  Domain Path: /languages
*/

if ( !defined('ABSPATH') ) exit;


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


    // Last Modified and 304 Snippet
    if( get_option('wdss_last_modified_n_304', '0') ) {
      add_action( 'init', array($this, 'wdss_last_modified') );
    }

    // Force Lowercase URLs Snippet
    if( get_option('wdss_force_lowercase', '0') ) {
      add_action( 'init', array($this, 'wdss_force_lowercase') );
    }    
  }


  function wdss_last_modified() {
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
  }


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


  function wdss_admin_scripts_loader() {
    wp_enqueue_style( 'wdss-style', plugin_dir_url(__FILE__) . 'style.css', array(), wp_rand() );
    wp_enqueue_script( 'wdss-script', plugin_dir_url(__FILE__) . 'main.js', array(), wp_rand(), true );    
  }

  function wdss_site_scripts_loader() {
    wp_enqueue_style( 'wdss-style', plugin_dir_url(__FILE__) . 'style.css', array(), wp_rand() );
  }


  function wdss_snippets() {

    if( get_option('wdss_last_modified_n_304', '0') ) {
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

    }

    // Remove redundant links Snippet
    if( get_option( 'wdss_redundant_links', '0' ) ) {
      remove_action( 'wp_head', 'feed_links_extra', 3 ); 
      remove_action( 'wp_head', 'feed_links', 2 ); 
      remove_action( 'wp_head', 'rsd_link' ); 
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
        $words_limit = get_option('wdss_title_words_limit', '6'); // max title`s words amount 
        $symbols_limit = get_option('wdss_word_chars_limit', '35'); // max title`s symbols amount 
        $ending = ' ' . get_option('wdss_title_ending') . ''; // adds title`s ending
        $ending_exists = boolval(strpos($post_title, $ending));
        $title_less_than_count = boolval(mb_strlen($post_title) < $symbols_limit);
        $is_already_exists = $title_less_than_count && $ending_exists;
        
        if ( is_front_page() || is_archive() || is_category() || $is_already_exists )   { // where`s title trimming shouldn`t be implemented
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

    // AMP Redirects Rules Snippet
    if( get_option('wdss_amp_rules', '0') ) {
      function wdss_amp_plugin_change_behavior($url, $status) {
        if (!is_admin()) {
  
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
          if (!is_admin()) {
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
  
  
      if ($_SERVER['REQUEST_URI'] == "/index.html"  || 
          $_SERVER['REQUEST_URI'] == "/index.php"   || 
          $_SERVER['REQUEST_URI'] == "/amp"         || 
          $_SERVER['REQUEST_URI'] == "/?amp"        || 
          $_SERVER['REQUEST_URI'] == "/?amp/"       || 
          $_SERVER['REQUEST_URI'] == "//?amp/"
        ) {
        header("Location: /", TRUE, 301);
        exit();
      }
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
      add_action( 'wp', 'wdss_force_410' );
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

  function number_handler_html($args) { ?>
    <input type="number" name="<?= $args['field_name']; ?>" min="<?= $args['min']; ?>" max="<?= $args['max']; ?>" value="<?= esc_attr(get_option($args['field_name']));?>" >  
  <? }


  function wdss_form_handler() {
    if(wp_verify_nonce($_POST['wfp_nonce'], 'wdss_save_settings') && current_user_can('manage_options')) {

      update_option('wdss_last_modified_n_304', sanitize_text_field($_POST['wdss_last_modified_n_304']));   
      update_option('wdss_redundant_links', sanitize_text_field($_POST['wdss_redundant_links']));   
      update_option('wdss_disable_autoupdates', sanitize_text_field($_POST['wdss_disable_autoupdates']));
      update_option( 'wdss_disable_admin_notices', sanitize_text_field( $_POST['wdss_disable_admin_notices'] ));
      
      update_option('wdss_yoast_schema', sanitize_text_field($_POST['wdss_yoast_schema']));   
      update_option('wdss_yoast_canonical_fix', sanitize_text_field($_POST['wdss_yoast_canonical_fix']));   
      update_option('wdss_autoptimize_lazy_fix', sanitize_text_field($_POST['wdss_autoptimize_lazy_fix']));   
      update_option('wdss_gutenberg_styles', sanitize_text_field($_POST['wdss_gutenberg_styles']));   
      update_option('wdss_amp_rules', sanitize_text_field($_POST['wdss_amp_rules']));  
      update_option('wdss_amp_fix', sanitize_text_field($_POST['wdss_amp_fix']));   
      update_option('wdss_force_lowercase', sanitize_text_field($_POST['wdss_force_lowercase']));  
      
      update_option('wdss_enable_title_clipping', sanitize_text_field($_POST['wdss_enable_title_clipping']));   
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
                
                  <div id="wdss-redundant-links" class="wdss-setting-item">
                      <label>
                        <span>Remove links (rest, feed, wlwmanifest etc)</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_redundant_links']); 
                          if( get_option('wdss_redundant_links') == '' ) update_option( 'wdss_redundant_links', '0' );               
                        ?>    
                    </label>
                  </div>

                  <div id="wdss-disable-autoupdates" class="wdss-setting-item">
                      <label>
                        <span>Disable plugins & themes automatic updates</span>
                        <?php 
                          $this->checkbox_handler_html(['field_name' => 'wdss_disable_autoupdates']); 
                          if( get_option('wdss_disable_autoupdates') == '' ) update_option( 'wdss_disable_autoupdates', '0' );               
                        ?>    
                    </label>
                  </div>

                  <div id="wdss-disable-admin-notices" class="wdss-setting-item">
                      <label>
                        <span>Disable admin notices</span>
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
                        <span>AMP & Homepage Redirect Rules</span>
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
            <h2>Long Title Clipping Settings <small>(posts & pages only)</small></h2>
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
                  <div id="wdss-words-limit" class="wdss-setting-item">
                  <label>
                        <span>Words Limit (default 6)</span>
                        <?php 
                          $default_words_limit = '6';
                          $this->number_handler_html(['field_name' => 'wdss_title_words_limit', 'min' => '2', 'max' => '7']); 
                          if( get_option('wdss_title_words_limit') == '' ) update_option( 'wdss_title_words_limit', $default_words_limit );               
                        ?>    
                    </label>
                  </div>   

                  <div id="wdss-chars-limit" class="wdss-setting-item">
                      <label>
                        <span>Chars per Word Limit (default: 35)</span>
                        <?php 
                          $default_chars_limit = '35';
                          $this->number_handler_html(['field_name' => 'wdss_word_chars_limit', 'min' => '32', 'max' => '50']); 
                          if( get_option('wdss_word_chars_limit') == '' ) update_option( 'wdss_word_chars_limit', $default_chars_limit );               
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