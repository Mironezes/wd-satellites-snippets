const isPluginPage = document.querySelector('#wdss-settings-page');

const titleClippingSection = {
  toggler: '#wdss-title-clipping-condition input',
  target: '#wdss-title-clipping-group'
};

const featuredImageSection = {
  toggler: '#wdss-auto-featured-image-condition input',
  target: '#wdss-featured-images-group'
};

const polylangSection = {
  toggler: '#wdss-polylang-meta-data-conditions input',
  target: '#wdss-polylang-meta-data-group'  
};

const cutTitleClippingReset = {
  button: '#wdss-title-clipping-excluded button.reset',
  target: '#wdss-title-clipping-excluded input'
};

const cutTitleSinceReset = {
  button: '#wdss-title-clipping-by-date button.reset',
  target: '#wdss-title-clipping-by-date input'
};

const featuredImagesListReset = {
  button: '#wdss-featured-images-group button.reset',
  target: '#wdss-featured-images-group input'
};



function resetValue(item) {
  const button = document.querySelector(item.button);
  const target = document.querySelector(item.target);

  button.addEventListener('click', () => {
    if( target.value !== "" && confirm('Are you sure?') ) {
      target.value = "";
    }
  });
}


function sectionToggler(section) {

  const toggler = document.querySelector(section.toggler);
  const isEnabled = toggler.hasAttribute('checked');
  const target = document.querySelector(section.target);
  
  if (isEnabled) {
    target.classList.toggle('visible');
  }
  
  toggler.addEventListener('click', () => {
    target.classList.toggle('visible');
  });
}


function toggleAccordion() {
  const accordions = Array.from(document.querySelectorAll('.wdss-setting-item-accordion'));

  accordions.forEach(accordion => {

    let content = accordion.nextElementSibling;

    accordion.addEventListener('click', () => {
      if(content.classList.contains('active')) {
        accordion.classList.remove('opened');
        content.classList.remove('active');
      }
      else {
        accordion.classList.add('opened');
        content.classList.add('active');
      }
    });
  });
}


function toggleCheckbox() {
  const inputs = Array.from(document.querySelectorAll('input[type="checkbox"'));
  inputs.forEach((input) => {
    input.addEventListener('click', () => {
      
      if(input.hasAttribute('checked')) {
        input.removeAttribute('checked');
        input.value = 0;
        input.checked = false;
      }
      else {
        input.setAttribute('checked', 'checked');
        input.checked = true;
        input.value = 1;
      }
    })
  });
}


function toggleAllOptions() {
  const inputs = Array.from(document.querySelectorAll('#wdss-snippets-settings input'));
  let toggler = document.querySelector('#wdss-toggle-options');

  function uncheckAll() {
    inputs.forEach((input) => {
        input.removeAttribute('checked');
        input.checked = false;
    });  
  }

  function checkAll() {
    inputs.forEach((input) => {
        input.setAttribute('checked', 'checked');
        input.checked = true;
    });  
  }

  toggler.addEventListener('click', () => {
    if(inputs[0].hasAttribute('checked')) {
      uncheckAll();
    }
    else {
      checkAll();
    }
  });
}


function getSiteTitle() {
  const btn = document.querySelector('#wdss-get-title');
  const input = document.querySelector('#wdss-title-ending input');
  const siteTitle = wdss_localize.site_title;

  btn.addEventListener('click', () => input.value = siteTitle);
}


function getPostsModal() {
  const btn = document.querySelector('#wdss-title-clipping-excluded__choose');
  const context = document.querySelector('#wdss-title-clipping-excluded');

  btn.addEventListener('click', init);

  function init() {
    modalHandler.call(context, wdss_localize.posts_list);
  
    const posts = Array.from(document.querySelectorAll('.wdss-table-row.post'));
    posts.forEach(post => {
        post.addEventListener('click', () => {
          let checkbox = post.querySelector('.wdss-table-post__select input');
          if(checkbox.hasAttribute('checked')) {
            uncheck(checkbox);
          }
          else {
            check(checkbox);
          }
        });
    });

    function check(input) {
      input.setAttribute('checked', 'checked');
      input.checked = true; 
    }

    function uncheck(input) {
      input.removeAttribute('checked');
      input.checked = false;
    }
  }

  function modalHandler($content) {
    this.insertAdjacentHTML('beforeend', $content);
  
    const modal = this.querySelector('.wdss-modal');
    const closeBtn = modal.querySelector('.wdss-modal-header i');
    const saveBtn = modal.querySelector('.wdss-button.submit');
    const input = this.querySelector('#wdss-title-clipping-excluded input[type="text"]');
  
    if(input.value !== '') {
      let inputIdsArr = input.value.split(',');
      let checkboxes = Array.from(modal.querySelectorAll('input[type="checkbox"]'));
      checkboxes.forEach(checkbox => {
        if(inputIdsArr.includes(checkbox.value)) {
          checkbox.setAttribute('checked', 'checked');
          checkbox.checked = true;
        }
      });
    }

    closeBtn.addEventListener('click', () => modal.remove());
  
    saveBtn.addEventListener('click', () => {
      
      const posts = modal.querySelectorAll('.wdss-table-post__select input[type="checkbox"]:checked');
      let idsArr = [];
      let ids;
  
      posts.forEach(post => {
        idsArr.push(post.value);
      });
  
      ids = idsArr.join(',');
      input.value = ids;
      modal.remove()
    });
  }
}


function mediaFileChooser() {
  const btn = document.querySelector('#wdss-featured-images__choose');

  btn.addEventListener('click', function(e) {
      e.preventDefault();
      let image_frame;
      if (image_frame) {
          image_frame.open();
      }

      image_frame = wp.media({
          title: 'Select Featured Images',
          multiple: true,
          library: {
              type: 'image',
          },
          button: {
            text: 'Select'
          }
      });

      image_frame.on('close', function() {
          let selection = image_frame.state().get('selection');
          let gallery_ids = new Array();
          let my_index = 0;
          selection.forEach(function(attachment) {
              gallery_ids[my_index] = attachment['id'];
              my_index++;
          });
          let ids = gallery_ids.join(",");
          document.querySelector('#wdss-featured-images-list input').value = ids;
      });

      image_frame.on('open', function() {
          let selection = image_frame.state().get('selection');
          let ids = document.querySelector('#wdss-featured-images-list input').value.split(',');
          ids.forEach(function(id) {
              let attachment = wp.media.attachment(id);
              attachment.fetch();
              selection.add(attachment ? [attachment] : []);
          });

      });

      image_frame.open();
  });
}


function Init() {
  if(isPluginPage) {
    
    if(wdss_localize.total_post_count > 0) {
      sectionToggler(titleClippingSection);
      sectionToggler(featuredImageSection);
      getSiteTitle();
      mediaFileChooser();
    }

    if(wdss_localize.is_polylang_exists) {
      sectionToggler(polylangSection);  
    }

    toggleCheckbox();
    toggleAllOptions();
    toggleAccordion();

    resetValue(cutTitleClippingReset);
    resetValue(cutTitleSinceReset);
    resetValue(featuredImagesListReset);

    getPostsModal();

  }
}

document.addEventListener('DOMContentLoaded', Init);