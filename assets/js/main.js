/**
 * AW-Base Main scripts
 */
jQuery(document).ready(function($) {
    // Generate Table of Contents
    const $tocContainer = $('#awbase-toc-container');
    if ($tocContainer.length > 0) {
        const $headings = $('.entry-content h2, .entry-content h3');
        if ($headings.length > 0) {
            const $tocList = $tocContainer.find('.toc-list');
            let currentH2List = null;

            $headings.each(function(index) {
                const $heading = $(this);
                // Assign an ID if not exists
                let id = $heading.attr('id');
                if (!id) {
                    id = 'toc-heading-' + index;
                    $heading.attr('id', id);
                }

                const text = $heading.text();
                const nodeName = $heading.prop('nodeName').toLowerCase();
                const $li = $('<li>').append($('<a>').attr('href', '#' + id).text(text));

                if (nodeName === 'h2') {
                    $tocList.append($li);
                    currentH2List = $('<ul>');
                    $li.append(currentH2List);
                } else if (nodeName === 'h3') {
                    if (currentH2List) {
                        currentH2List.append($li);
                    } else {
                        // If h3 comes before any h2
                        $tocList.append($li);
                    }
                }
            });

            // Smooth scroll for TOC links
            $tocList.find('a').on('click', function(e) {
                e.preventDefault();
                const target = $(this.hash);
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80 // Adjust for fixed header if any
                    }, 500);
                }
            });
        } else {
            $tocContainer.hide(); // Hide TOC if no headings
        }
    }
});
