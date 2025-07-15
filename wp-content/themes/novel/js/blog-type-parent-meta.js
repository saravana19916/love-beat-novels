jQuery(document).ready(function ($) {
    function loadParentBlogs(blogTypeSlug) {
        const parentSelect = $('select[name="parent_blog_meta_field"]');

        if (!blogTypeSlug) {
            parentSelect.html('<option value="">Select Parent Blog</option>');
            return;
        }

        $.ajax({
            url: BlogTypeMetaAjax.ajax_url,
            method: 'POST',
            data: {
                action: 'fetch_parent_blogs',
                blogTypeSlug: blogTypeSlug,
                current_post_id: $('#post_ID').val() || 0,
                _ajax_nonce: BlogTypeMetaAjax.nonce
            },
            success: function (response) {
                console.log("res", response.data);
                if (response.success) {
                    parentSelect.html('<option value="">Select Parent Blog</option>');
                    response.data.forEach(function (post) {
                        parentSelect.append(`<option value="${post.ID}">${post.title}</option>`);
                    });
                } else {
                    parentSelect.html('<option value="">No parent blogs found</option>');
                }
            }
        });
    }

    jQuery(document).on('change', 'input.components-checkbox-control__input', function () {
        const isChecked = jQuery(this).is(':checked');
        const labelText = jQuery(this).closest('.components-base-control').find('label').text().trim();

        if (!isChecked) {
            loadParentBlogs(null);
            return;
        }

        if (labelText === 'Main Blog') {
            loadParentBlogs('main-blog');
        } else if (labelText === 'My Creation Blog') {
            loadParentBlogs('my-creation-blog');
        }
    });


    // Auto-load if taxonomy is already selected (like when editing a post)
    // const selectedBlogType = $('select#blog_type').val();
    // if (selectedBlogType) {
    //     loadParentBlogs(selectedBlogType);
    // }
});
