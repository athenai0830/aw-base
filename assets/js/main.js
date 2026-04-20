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

// Flying Scripts 等による遅延実行に対応: DOMContentLoaded が既に完了していれば即実行
function awbDOMReady(fn) {
    if (document.readyState !== 'loading') {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

awbDOMReady(function () {

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
                    currentH2List.className = 'toc-list';
                    li.appendChild(currentH2List);
                } else if (nodeName === 'h3') {
                    (currentH2List || tocList).appendChild(li);
                }
            });

            // CLS対策: コンテンツを埋め終わってから open にする
            tocContainer.open = true;

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
    // Blogcard: 外部リンクを非同期フェッチで差し替え
    // ----------------------------------------
    document.querySelectorAll('.awb-blogcard-placeholder').forEach(function (placeholder) {
        var url    = placeholder.dataset.url;
        var target = placeholder.dataset.target || '';
        if (!url) return;
        var endpoint = (window.awbaseData && window.awbaseData.restUrl)
            ? window.awbaseData.restUrl + 'awbase/v1/blogcard?url=' + encodeURIComponent(url) + '&target=' + encodeURIComponent(target)
            : '/wp-json/awbase/v1/blogcard?url=' + encodeURIComponent(url) + '&target=' + encodeURIComponent(target);
        fetch(endpoint)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data && data.html) {
                    var tmp = document.createElement('div');
                    tmp.innerHTML = data.html;
                    if (tmp.firstChild) {
                        placeholder.parentNode.replaceChild(tmp.firstChild, placeholder);
                    }
                }
            })
            .catch(function () { /* フェッチ失敗時はプレースホルダーのまま */ });
    });

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
    // Table horizontal scroll hint（ラッパー・ヒントは PHP 側でプリレンダリング済み）
    // ----------------------------------------
    // scrollWidth 確認後に is-hidden を付け外しするだけ
    function setupTableScrollHint(el) {
        var hint = el.previousElementSibling;
        if (!hint || !hint.classList.contains('table-scroll-hint')) return;
        if (el.scrollWidth > el.clientWidth + 1) {
            hint.classList.remove('is-hidden');
        } else {
            hint.classList.add('is-hidden');
        }
    }

    document.querySelectorAll('.table-scroll, .entry-content .wp-block-table').forEach(function (el) {
        el.addEventListener('scroll', function () {
            if (el.scrollLeft > 10) {
                var hint = el.previousElementSibling;
                if (hint && hint.classList.contains('table-scroll-hint')) {
                    hint.classList.add('is-hidden');
                }
            }
        }, { passive: true });
    });

    // CSS レイアウト確定後にヒントを初期化（DOMContentLoaded 直後は scrollWidth が未確定のため rAF×2 で遅延）
    function initTableScrollHints() {
        document.querySelectorAll('.table-scroll, .entry-content .wp-block-table').forEach(function (el) {
            setupTableScrollHint(el);
        });
    }
    requestAnimationFrame(function () { requestAnimationFrame(initTableScrollHints); });

    window.addEventListener('resize', function () {
        document.querySelectorAll('.table-scroll, .entry-content .wp-block-table').forEach(function (el) {
            setupTableScrollHint(el);
        });
    }, { passive: true });

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
            }, { passive: true });

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

