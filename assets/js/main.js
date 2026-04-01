/**
 * AW-Base Main scripts – vanilla JS (no jQuery)
 */

// ----------------------------------------
// Custom smooth scroll (easing, duration ms)
// ----------------------------------------
function awbSmoothScroll(targetY, duration) {
    var startY    = window.scrollY || window.pageYOffset;
    var distance  = targetY - startY;
    var startTime = null;
    function ease(t) { return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t; }
    function step(now) {
        if (!startTime) startTime = now;
        var p = Math.min((now - startTime) / duration, 1);
        window.scrollTo(0, startY + distance * ease(p));
        if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

document.addEventListener('DOMContentLoaded', function () {

    // ----------------------------------------
    // Table of Contents (TOC)
    // ----------------------------------------
    var tocContainer = document.getElementById('awbase-toc-container');
    if (tocContainer) {
        var headings = document.querySelectorAll('.entry-content h2, .entry-content h3');
        if (headings.length > 0) {
            var tocList = tocContainer.querySelector('.toc-list');
            var currentH2List = null;

            headings.forEach(function (heading, index) {
                var id = heading.getAttribute('id');
                if (!id) {
                    id = 'toc-heading-' + index;
                    heading.setAttribute('id', id);
                }
                var nodeName = heading.nodeName.toLowerCase();
                var li = document.createElement('li');
                var a  = document.createElement('a');
                a.href        = '#' + id;
                a.textContent = heading.textContent;
                li.appendChild(a);

                if (nodeName === 'h2') {
                    tocList.appendChild(li);
                    currentH2List = document.createElement('ul');
                    li.appendChild(currentH2List);
                } else if (nodeName === 'h3') {
                    (currentH2List || tocList).appendChild(li);
                }
            });

            // Smooth scroll for TOC links
            tocList.addEventListener('click', function (e) {
                var link = e.target.closest('a');
                if (!link) return;
                e.preventDefault();
                var dest = document.querySelector(link.getAttribute('href'));
                if (dest) {
                    var top = dest.getBoundingClientRect().top + window.pageYOffset - 80;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                }
            });
        } else {
            tocContainer.style.display = 'none';
        }
    }

    // ----------------------------------------
    // SNS Share: copy URL button
    // ----------------------------------------
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.sns-share-copy');
        if (!btn) return;
        var url = btn.dataset.url;
        if (navigator.clipboard && url) {
            navigator.clipboard.writeText(url).then(function () {
                document.querySelectorAll('.sns-share-copy').forEach(function (b) {
                    b.innerHTML = '<i class="fa-solid fa-check"></i>';
                });
                setTimeout(function () {
                    document.querySelectorAll('.sns-share-copy').forEach(function (b) {
                        b.innerHTML = '<i class="fa-solid fa-link"></i>';
                    });
                }, 2000);
            });
        }
    });

    // ----------------------------------------
    // Mobile hamburger menu
    // ----------------------------------------
    var navToggle = document.querySelector('.nav-toggle');
    if (navToggle) {
        navToggle.addEventListener('click', function () {
            var isOpen = navToggle.classList.contains('is-active');
            navToggle.classList.toggle('is-active');
            navToggle.setAttribute('aria-expanded', String(!isOpen));
            var drawer = document.getElementById('mobile-nav-drawer');
            if (drawer) {
                drawer.classList.toggle('is-open');
                drawer.setAttribute('aria-hidden', String(isOpen));
            }
        });
    }

    // ----------------------------------------
    // First View scroll button (ゆっくりスクロール)
    // ----------------------------------------
    var fvScrollBtn = document.getElementById('fv-scroll-btn');
    if (fvScrollBtn) {
        fvScrollBtn.addEventListener('click', function () {
            var fv = document.querySelector('.first-view');
            if (fv) {
                awbSmoothScroll(fv.offsetTop + fv.offsetHeight, 1200);
            }
        });
    }

    // ----------------------------------------
    // Back to top button (pagetop) – 常に表示
    // ----------------------------------------
    var pagetopBtn = document.getElementById('pagetop-btn');
    if (pagetopBtn) {
        pagetopBtn.addEventListener('click', function () {
            awbSmoothScroll(0, 1000);
        });
    }

    // ----------------------------------------
    // Table horizontal scroll wrapper
    // ----------------------------------------
    document.querySelectorAll('.entry-content table').forEach(function (table) {
        if (table.closest('.table-scroll') || table.closest('.wp-block-table')) return;
        var wrapper = document.createElement('div');
        wrapper.className = 'table-scroll';
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    });

    // スクロール可能なテーブルに is-overflowing クラスを付け右端フェードを制御
    function updateTableOverflow(el) {
        var overflows = el.scrollWidth > el.clientWidth + 1;
        var atEnd     = el.scrollLeft + el.clientWidth >= el.scrollWidth - 2;
        el.classList.toggle('is-overflowing', overflows && !atEnd);
    }

    document.querySelectorAll('.table-scroll, .entry-content .wp-block-table').forEach(function (el) {
        updateTableOverflow(el);
        el.addEventListener('scroll', function () { updateTableOverflow(el); });
    });

    window.addEventListener('resize', function () {
        document.querySelectorAll('.table-scroll, .entry-content .wp-block-table').forEach(function (el) {
            updateTableOverflow(el);
        });
    });

    // ----------------------------------------
    // Popular list slider (矢印表示のみ・幅はCSS任せ)
    // ----------------------------------------
    function initPopularSliders() {
        document.querySelectorAll('.popular-scroll-wrap').forEach(function (wrap) {
            var list    = wrap.querySelector('.popular-card-slim-list');
            var prevBtn = wrap.querySelector('.popular-prev-btn');
            var nextBtn = wrap.querySelector('.popular-next-btn');
            if (!list) return;

            var total = parseInt(wrap.dataset.total || '0', 10);
            var GAP = 14;

            function getVisible() {
                if (window.innerWidth < 600) return 2;
                if (window.innerWidth < 900) return 3;
                return 5;
            }

            // コンテナの実際の幅からカード幅を計算してセット
            function setCardWidths() {
                var visible = getVisible();
                var containerW = wrap.offsetWidth;
                var cardW = Math.floor((containerW - GAP * (visible - 1)) / visible);
                list.querySelectorAll('.popular-card-slim').forEach(function (card) {
                    card.style.flex = '0 0 ' + cardW + 'px';
                    card.style.width = cardW + 'px';
                });
            }

            function updateArrows() {
                var show = total > getVisible();
                if (prevBtn) prevBtn.style.display = show ? '' : 'none';
                if (nextBtn) nextBtn.style.display = show ? '' : 'none';
            }

            setCardWidths();
            updateArrows();

            var resizeTimer;
            window.addEventListener('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function () {
                    setCardWidths();
                    updateArrows();
                }, 120);
            });

            if (prevBtn) {
                prevBtn.addEventListener('click', function () {
                    var card = list.querySelector('.popular-card-slim');
                    var step = card ? card.offsetWidth + GAP : list.offsetWidth;
                    list.scrollBy({ left: -step * getVisible(), behavior: 'smooth' });
                });
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', function () {
                    var card = list.querySelector('.popular-card-slim');
                    var step = card ? card.offsetWidth + GAP : list.offsetWidth;
                    list.scrollBy({ left: step * getVisible(), behavior: 'smooth' });
                });
            }
        });
    }
    // 描画完了後に確実に初期化
    requestAnimationFrame(function () { requestAnimationFrame(initPopularSliders); });

});

