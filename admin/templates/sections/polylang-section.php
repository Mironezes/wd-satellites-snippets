<?php 
  $post_counts = wp_count_posts( 'post' )->publish;
  $polylang = function_exists('pll_languages_list') ? count(pll_languages_list()) : false;
  if($polylang && $post_counts > 1)  : 
?>
<section id="polylang-settings" class="wdss-section">
  <div class="wdss-section-header">
    <h2 class="section-toggler">Polylang </h2>
    <div class="wdss-section-header-togglers">
      <i title="Pin this section as open" class=" lock section-pin"></i>
      <i class=" chevron-down section-toggler"></i>
    </div>
  </div>
  <div class="wdss-row hidden">
    <div class="wdss-section-content">
      <div id="wdss-polylang-meta-data-condition" class="wdss-setting-item">
          <label>
              <span>Custom meta descriptions</span>
              <?php 
                checkbox_handler_html(['field_name' => 'wdss_polylang_meta_data']); 
                if( get_option('wdss_polylang_meta_data') == '' ) update_option( 'wdss_polylang_meta_data', '0' );               
              ?>    
          </label>
      </div>

      <div id="wdss-polylang-meta-data-group" class="wdss-setting-group hidden">    
        <div id="wdss-polylang-homepage-title" class="wdss-setting-item wdss-polylang-accordion">
          <div class="wdss-setting-item-accordion">
            <h3>Homepage title</h3>
            <i class=" chevron-down section-toggler"></i>
          </div>

          <?php $polylang_lang_list = pll_languages_list(['fields' => []]);  ?>

            <div class="wdss-setting-item-accordion-content">
                  <?php foreach($polylang_lang_list as $lang) : ?>
                    <div class="wdss-polylang-textarea-block">
                      <strong><?= $lang->name; ?></strong>
                        <?php 
                          textarea_handler_html([
                            'field_name' => 'wdss_polylang_home_title_' . $lang->slug . '',
                            'rows_count' => 2
                          ]);        
                        ?>
                    </div>
                  <?php endforeach;  ?>
            </div>
        </div>

        <div id="wdss-polylang-homepage-description" class="wdss-setting-item wdss-polylang-accordion">
          <div class="wdss-setting-item-accordion">
            <h3>Homepage description</h3>
            <i class=" chevron-down section-toggler"></i>
          </div>

          <?php $polylang_lang_list = pll_languages_list(['fields' => []]);  ?>

            <div class="wdss-setting-item-accordion-content">
                  <?php foreach($polylang_lang_list as $lang) : ?>
                    <div class="wdss-polylang-textarea-block">
                      <strong><?= $lang->name; ?></strong>
                        <?php 
                          textarea_handler_html(['field_name' => 'wdss_polylang_home_desc_' . $lang->slug . '']);        
                        ?>
                    </div>
                  <?php endforeach;  ?>
            </div>
        </div>

        <?php 
          $total_posts = wp_count_posts('post')->publish;
          if($total_posts > 0) : 
        ?>
          <div id="wdss-polylang-author-title" class="wdss-setting-item wdss-polylang-accordion">
            <div class="wdss-setting-item-accordion">
              <h3>Author`s page title</h3>
              <i class=" chevron-down section-toggler"></i>
            </div>

            <div class="wdss-setting-item-accordion-content">
              <?php
                $authors = get_users( array( 'fields' => array( 'ID', 'display_name', 'user_nicename' ), 'has_published_posts' => 'post' ) );
                foreach($authors as $author) : 
              ?>    
              <div class="wdss-polylang-author-block">
                <h4><i class=" user"></i> <?= $author->display_name; ?></h4>
                <div class="wdss-polylang-textarea-blocks">
                  <?php foreach($polylang_lang_list as $lang) : ?>
                  <div class="wdss-polylang-textarea-block">
                    <strong><?= $lang->name; ?></strong>
                      <?php 
                        textarea_handler_html([
                          'field_name' => 'wdss_polylang_author_title_' . $author->user_nicename . '_'. $lang->slug . '',
                          'rows_count' => 2
                        ]);       
                      ?>
                  </div>
                  <?php endforeach;  ?>
                </div>
              </div>
              <?php endforeach; ?>   
            </div>
          </div>
      
          <div id="wdss-polylang-author-description" class="wdss-setting-item wdss-polylang-accordion">
            <div class="wdss-setting-item-accordion">
              <h3>Author`s page description</h3>
              <i class=" chevron-down section-toggler"></i>
            </div>

            <div class="wdss-setting-item-accordion-content">
              <?php
                $authors = get_users( array( 'fields' => array( 'ID', 'display_name', 'user_nicename' ), 'has_published_posts' => 'post' ) );
                foreach($authors as $author) : 
              ?>    
              <div class="wdss-polylang-author-block">
                <h4><i class=" user"></i> <?= $author->display_name; ?></h4>
                <div class="wdss-polylang-textarea-blocks">
                  <?php foreach($polylang_lang_list as $lang) : ?>
                    <div class="wdss-polylang-textarea-block">
                      <strong><?= $lang->name; ?></strong>
                      <?php 
                        textarea_handler_html(['field_name' => 'wdss_polylang_author_desc_' . $author->user_nicename . '_'. $lang->slug . '']);       
                      ?>
                    </div>
                  <?php endforeach;  ?>
                </div>
              </div>
              <?php endforeach; ?>   
            </div>
          </div>
          <?php endif; ?>
      </div>
      <div id="wdss-multilang-sitemap-condition" class="wdss-setting-item">
        <label>
            <span>Use Multilang Sitemap<br> instead of Yoast</span>
            <?php 
              checkbox_handler_html(['field_name' => 'wdss_multilang_sitemap']); 
              if( get_option('wdss_multilang_sitemap') == '' ) update_option( 'wdss_multilang_sitemap', '0' );               
            ?>    
        </label>
      </div>
  </div>
</section>
<?php 
  else : 
    update_option('wdss_polylang_meta_data', '0');
    update_option('wdss_multilang_sitemap', '0');
  endif; 
?>