<?php include_once $_SERVER['DOCUMENT_ROOT']."/_common.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link href="https://fonts.cdnfonts.com/css/montserrat" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <style>
        body {margin: 0;}
        * {box-sizing: border-box;}
        .slick-slider {width: 100%;}
        .slick-slide {height: 320px; border-radius: 20px; overflow: hidden; width: 896px !important; max-width: 90vw; margin: 0 12px; position: relative;}
        .slick-slide img {width: 100%; height: 100%; object-fit: cover;}
        .slick-slide::after {content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); z-index: 1; transition: opacity 0.3s;}
        .slick-center::after {opacity: 0;}
        .slick-dots {bottom: 20px; width: fit-content; left: 50%; transform: translateX(-50%); margin-left: -32px;}
        .slick-dots li {width: 8px; height: 8px; margin: 0 4px; background: rgba(46,46,46,0.4); border-radius: 50%;}
        .slick-dots li.slick-active {background: rgba(215,215,215,0.8);}
        .slick-dots li > a > span {display: none;}
        .slick-prev, .slick-next {width: 40px; height: 40px; z-index: 2; background: rgba(240,240,240,0.7); border: 1px solid rgba(240 240 240 / 30%); border-radius: 20px;}
        .slick-prev {left: calc((100% - 856px) / 2); transform: translateY(-50%);}
        .slick-next {right: calc((100% - 856px) / 2); transform: translateY(-50%);}
        .slick-prev:before, .slick-next:before {display: block; content: ""; width: 10px; height: 10px; border-color: rgba(60, 60, 60, 0.7); border-width: 2px 2px 0 0; border-style: solid; position: absolute; top: 50%;}
        .slick-prev:before {transform: translate(15px, -50%) rotate(-135deg);}
        .slick-next:before {transform: translate(11px, -50%) rotate(45deg);}
        .slick-prev:hover,
        .slick-prev:focus,
        .slick-next:hover,
        .slick-next:focus {
            background: rgba(240,240,240,0.7);
        }
        .ms-pause {position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%); background: rgba(46,46,46,0.4); color: white; border: none; padding: 12px 10px 12px 14px; border-radius: 8px; cursor: pointer; z-index: 3; margin-left: 32px;}
        .ms-pause::before {content: ''; position: absolute; top: 50%; left: 43%; transform: translate(-70%, -50%); width: 2px; height: 10px; background: #fff; border: none; box-shadow: 5px 0 0 #fff; transition: all 0.3s;}
        .ms-pause.on::before {width: 0; height: 0; background: transparent; box-shadow: none; border-left: 8px solid #fff; border-top: 5px solid transparent; border-bottom: 5px solid transparent; transform: translate(-10%, -50%); transition: all 0.3s;}
        .ms-pause:hover {background: rgba(46,46,46,0.85);}
        .stats-slide {background: #ffdddd; padding: 20px; display: flex; justify-content: center; align-items: center; height: 100%; background: url('/image/main/202504_slide_scalingUp.png') no-repeat center center;}
        .num-item-wrap {display: flex; gap: 20px; max-width: 896px; height: 280px;}
        .num-item-wrap > div {display: flex; flex-wrap: wrap; flex: 1; align-items: flex-start; gap: 24px; padding: 172px 0 0 60px;}
        .num-item {display: flex; align-items: center; width: auto; line-height: 1;}
        .num-item img {width: 40px; height: 40px;}
        .num-item .nums {font-size: 28px; font-weight: 400; color: #fff; font-family: 'montserrat', sans-serif;}
        .num-item .in-title {font-size: 18px; color: rgba(255, 255, 255, 0.7); margin: 0 0 10px 0; font-family: 'montserrat', sans-serif; font-weight: 400;}
        .num-item .num-unit {font-size: 22px; color: #fff; font-family: 'montserrat', sans-serif;}
    </style>

    <style>
        .top-deals { padding: 40px 0; background: #fff; }
        .container { width: 90%; max-width: 1200px; margin: 0 auto; }
        .section-title { font-size: 24px; font-weight: bold; margin-bottom: 4px; }
        .section-subtitle { font-size: 14px; color: #555; margin-bottom: 20px; }
        .deal-tabs { display: flex; gap: 12px; margin-bottom: 30px; flex-wrap: wrap; }
        .tab { padding: 10px 16px; border: none; border-radius: 999px; background: #f3f3f3; cursor: pointer; font-size: 14px; }
        .tab.active { background: #007aff; color: #fff; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
        .product-card { border: 1px solid #eee; border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 10px; }
        .badges { display: flex; gap: 6px; flex-wrap: wrap; }
        .badge { font-size: 12px; padding: 2px 8px; border-radius: 8px; color: #fff; }
        .badge.top-picks { background: #ff9900; }
        .badge.limited { background: #007aff; }
        .badge.clearance { background: #ff3d57; }
        .badge.back { background: #a172ff; }
        .badge.new { background: #28c76f; }
        .image { height: 120px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999; font-size: 14px; border-radius: 8px; }
        .product-info { display: flex; flex-direction: column; gap: 4px; }
        .product-title { font-size: 14px; font-weight: 500; }
        .options { font-size: 12px; color: #888; }
        .price { font-size: 16px; font-weight: bold; color: #000; }
        .discount { font-size: 13px; color: #e53935; margin-left: 6px; }
        .add-to-cart { margin-top: 8px; padding: 8px; font-size: 14px; border: none; background: #007aff; color: white; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <?php
    $nData_brand = sql_fetch(" Select count(a.IDX) as CNT from 2011_brandInfo as a left join 2011_makerInfo as b on a.MKIDX = b.IDX left join (select BRIDX,count(BRIDX) as BRcount from 2011_productInfo where Pdeleted = 0 group by BRIDX) as c on a.IDX=c.BRIDX left join (select BRIDX,count(BRIDX) as BRcount2 from 2011_productInfo where Pagree = 1 and Pstate = 1 and Pdeleted = 0 group by BRIDX) as d on a.IDX=d.BRIDX where BRdeleted=0 and BRshop='1000u'  ");
    ?>
    <div class="matinTopSlide">
        <div class="main_slider slider">
            <a target="_parent" href="/member/login.html"><img src="/image/main/bannersample2.png" alt="Login Banner"></a>
            <a target="_parent" href="/product/bests.html"><img src="/image/main/bannersample3.png" alt="Best Products"></a>
            <a target="_parent" href="/product/list.html?searchKind=&s1=kpopgoods"><img src="/image/main/202501_slide_secure.png" alt="K-Pop Goods"></a>
            <a target="_parent" href="/customer/faq.html"><img src="/image/main/bannersample5.png" alt="FAQ"></a>
            <a target="_parent" href="/product/list.html?searchKind=&s1=plushbagcharm"><img src="/image/main/202504_slide_keyring.png" alt="Plush Charm"></a>
            <div class="stats-slide">
                <div class="num-item-wrap">
                    <div>
                        <div class="num-item" style="width: 90px;">
                            <div>
                                <h4 class="in-title">Products</h4>
                                <span class="nums" data-count="35000">0</span><span class="num-unit">+</span><br>
                            </div>
                        </div>
                        <div class="num-item" style="width: 96px;">
                            <div>
                                <h4 class="in-title">Categories</h4>
                                <span class="nums" data-count="275">0</span><br>
                            </div>
                        </div>
                        <div class="num-item" style="width: 64px;">
                            <div>
                                <h4 class="in-title">Brands</h4>
                                <span class="nums" data-count="<?=$nData_brand[CNT]?>">0</span><br>
                            </div>                                                                                                
                        </div>
                        <div class="num-item" style="width: 132px;">
                            <div>
                                <h4 class="in-title" style="font-size: 15px;">Warehouse (sq ft)</h4>
                                <span class="nums" data-count="78284">0</span><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="ms-pause"></button>
    </div>

    <section class="top-deals">
        <div class="container">
            <h2 class="section-title">
                <span class="emoji">💖</span> Top Deals You’ll Love
            </h2>
            <p class="section-subtitle">Curated deals just for you!</p>
        
            <!-- Filter Tabs -->
            <div class="deal-tabs">
                <button class="tab active" data-filter="top-picks">⭐ Top Picks</button>
                <button class="tab" data-filter="limited">⏰ Limited Offer</button>
                <button class="tab" data-filter="clearance">🎁 Clearance Sale</button>
                <button class="tab" data-filter="back">📦 Back in Stock</button>
            </div>
        
            <!-- Product Cards -->
            <div class="product-grid">
                <div class="product-card top-picks">
                    <div class="badges">
                        <span class="badge top-picks">Top Picks</span>
                        <span class="badge new">NEW</span>
                    </div>
                    <div class="image">[Image Placeholder]</div>
                    <div class="product-info">
                        <h3 class="product-title">Shin-chan Crayon Multi Pocket Pen Pouch</h3>
                        <p class="options">15EA / 10EA</p>
                        <p class="price">USD$ 5.31 <span class="discount">45%~50%</span></p>
                        <button class="add-to-cart">🛒 Add to Cart</button>
                    </div>
                </div>
        
                <div class="product-card limited">
                    <div class="badges">
                        <span class="badge limited">Limited Offer</span>
                        <span class="badge new">NEW</span>
                    </div>
                    <div class="image">[Image Placeholder]</div>
                    <div class="product-info">
                        <h3 class="product-title">JINILAND Wasabi Bear 4 Cut 4x6 Collect Book</h3>
                        <p class="options">8EA / 10EA</p>
                        <p class="price">USD$ 6.78 <span class="discount">40%~43%</span></p>
                        <button class="add-to-cart">🛒 Add to Cart</button>
                    </div>
                </div>
        
                <div class="product-card clearance">
                    <div class="badges">
                        <span class="badge clearance">Clearance Sale</span>
                    </div>
                    <div class="image">[Image Placeholder]</div>
                    <div class="product-info">
                        <h3 class="product-title">Shin-chan Crayon Multi Pocket Pen Pouch</h3>
                        <p class="options">15EA / 10EA</p>
                        <p class="price">USD$ 5.31 <span class="discount">45%~50%</span></p>
                        <button class="add-to-cart">🛒 Add to Cart</button>
                    </div>
                </div>
        
                <div class="product-card back">
                    <div class="badges">
                        <span class="badge back">Back in Stock</span>
                    </div>
                    <div class="image">[Image Placeholder]</div>
                    <div class="product-info">
                        <h3 class="product-title">JINILAND Wasabi Bear 4 Cut 4x6 Collect Book</h3>
                        <p class="options">8EA / 10EA</p>
                        <p class="price">USD$ 6.78 <span class="discount">40%~43%</span></p>
                        <button class="add-to-cart">🛒 Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    

    <script>
        $(function(){
            $('.main_slider').slick({
                slidesToShow: 1,
                autoplay: true,
                autoplaySpeed: 3500,
                dots: true,
                centerMode: true,
                variableWidth: true,
                arrows: true,
                customPaging: function(slider, i) {
                    return '<a href="#"><span>' + (i + 1) + '</span></a>';
                }
            });

            function animateNumbers() {
                $('.nums').each(function() {
                    const $this = $(this);
                    const countTo = $this.attr('data-count');
                    $({ countNum: 0 }).animate({
                        countNum: countTo
                    }, {
                        duration: 2000,
                        easing: 'swing',
                        step: function() {
                            $this.text(Math.floor(this.countNum));
                        },
                        complete: function() {
                            $this.text(this.countNum);
                        }
                    });
                });
            }

            $('.main_slider').on('afterChange', function(event, slick, currentSlide) {
                if (currentSlide === slick.slideCount - 1) {
                    $('.nums').text(0);
                    animateNumbers();
                }
            });

            if ($('.main_slider').slick('slickCurrentSlide') === $('.main_slider').slick('getSlick').slideCount - 1) {
                animateNumbers();
            }

            // 재생/일시정지 토글
            let paused = false;
            $('.ms-pause').click(function(){
            if (!paused) {
                $(this).addClass('on');
                $('.main_slider').slick('slickPause');
                paused = true;
            } else {
                $(this).removeClass('on');
                $('.main_slider').slick('slickPlay');
                paused = false;
            }
            });
        });
    </script>

    <script>
        document.querySelectorAll('.deal-tabs .tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.deal-tabs .tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const filter = tab.getAttribute('data-filter');
                document.querySelectorAll('.product-card').forEach(card => {
                if (card.classList.contains(filter)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
                });
            });
        });

        // 초기 상태에서 top-picks만 보이게 설정
        window.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.product-card').forEach(card => {
                card.style.display = card.classList.contains('top-picks') ? 'flex' : 'none';
            });
        });
    </script>
</body>
</html>