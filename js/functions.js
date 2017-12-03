function searchBox(){
  $('#header_left_btn').on('click', function(e){
    // e.preventDefault();
    $(this).parent().find('#text_box').focus();
  });

  $('#header_search_box').on('submit', function(e){
    var value = $(this).find('#text_box').val();
    if (value === "") {
      return false;
    }
  });
}

function readyAnimation(){

  $('.loader').css("transform","translateY(-100%)")
              .css("opacity","0");

  $title = $('#title');
  $title.addClass('activated');

  $main = $('#main');
  $main.addClass('ready');

}

function openingAnimation(){

  setTimeout(function() {
  $('.top-title .a1,.top-title .a2').addClass('animated');
  setTimeout(function() {
        $('.top-title-copy .a1,.top-title-copy .a2').addClass('animated');
    }, 300);
  (function(){
    var search_box = document.getElementById("search_box-wrapper");
    var tag_primary = document.getElementById("tag_primary");
    var scroll_instraction = document.getElementById("scroll_instraction");
    TweenLite.fromTo(
      search_box,
      1,
      {transform:"translateY(50px)", opacity:0},
      {transform:"translateY(0px)", opacity:1}
    );
    TweenLite.fromTo(
      tag_primary,
      1,
      {transform:"translateY(50px)", opacity:0},
      {transform:"translateY(0px)", opacity:1, delay:.3}
    );
    // TweenLite.fromTo(
    //   scroll_instraction,
    //   1,
    //   {opacity:0},
    //   {opacity:1, delay:3}
    // );
  }());
  }, 300);
}


// スマートヘッダー
// function smart_header(){
//   var $win = $(window),
//       $header = $('header'),
//       headerHeight = $header.outerHeight(),
//       startPos = 0;
//
//   $win.on('scroll', function() {
//     var value = $(this).scrollTop();
//     if ( value > startPos && value > headerHeight ) {
//       $header.css('transform', 'translateY(-100%)');
//     } else {
//       $header.css('transform', 'translateY(0)');
//     }
//     startPos = value;
//   });
// }

// トップページ用ヘッダー
function topPageHeader(){
  var $win = $(window),
      $header = $('#header'),
      topHeight = $('.top-visual').outerHeight();

  $win.on('load scroll draw', function() {
    var value = $(this).scrollTop(),
        $body = $('body');

    if ( value < topHeight ) {
      if ($body.hasClass('draw')) {
        $header.css('transform', 'translate(-300px,-100%)');
      }else {
        $header.css('transform', 'translate(0,-100%)');
      }
    } else {
      if ($body.hasClass('draw')) {
        $header.css('transform', 'translate(-300px,0)');
      }else {
        $header.css('transform', 'translate(0,0)');
      }
    }
  });
}



// プロフィールの開閉処理
function switchActivation(obj){


  if( !$(obj._this).hasClass("activated") ){

    // 他を閉じる
    $('.unfolder.activated').removeClass('activated');

    // 位置調整
    var position = $(obj._this).offset().top - 100;
    $('html,body').animate({scrollTop: position}, 200, 'swing');

    // クラス追加
    $(obj._this).addClass('activated');

    // 閉じるボタンの起動
    touchEventRegister({
      "target": $('.close'),
      "action": function(obj){
        obj._e.stopPropagation();
        $('.unfolder.activated').removeClass('activated');
      },
      "action_arg": {}
    });
  }
}


// スクロールイベントにより発火するアニメーション
function scrollAnimation(){

  //起動時に発火
  measureToAction();

  //スクロールイベントでも発火
  $(window).scroll(function(){
    measureToAction();
  });

  //メイン処理
  function measureToAction(){
    // ループ処理
    $('.unfolder:not(.scrolled)').each(function(){
      var targetPos = $(this).offset().top; // ターゲット要素の位置（上端）
      var scroll = $(window).scrollTop(); // 画面の位置（上端）
      var windowHeight = $(window).height(); // 画面の高さ
      if (scroll > targetPos - windowHeight - windowHeight/2){
        var $icon = $(this).find(".icon");
        var imgsrc = $icon.attr("data-img-src");
        $icon.css("background-image","url("+imgsrc+")");
        $(this).addClass('scrolled');
      }
    });
  }//measureToAction

}//end



// 上に戻る
function hrefScroll(){
  touchEventRegister({
    "target": $("a[href^='#']"),
    "action": function(obj){
      var target = $(obj._this).attr('href');
      var targetPos = $(target).offset().top;
      $('html,body').animate({scrollTop: targetPos-80}, 200, 'swing');
      return false;
    },
    "action_arg": {}
  });
}



// タッチイベント
function touchEventRegister(obj){

  // 1.デバイスによってイベントリスナーを分岐
  var userAgent = window.navigator.userAgent.toLowerCase();

  if (userAgent.indexOf('ipad') != -1 || userAgent.indexOf('iphone') != -1 || userAgent.indexOf('Android') != -1 ) {

    var start = 'touchstart';
    var move = 'touchmove';
    var end = 'touchend';

  }else{

    var start = 'mousedown';
    var move = 'mousemove';
    var end = 'mouseup';

  };

  // 2.ドラッグの有無によりコールバック関数の発動を分岐
  $(obj.target).on( start ,function(e){
    $(this).on( end ,function(e){

      // コールバックの引数を用意
      obj.action_arg._this = $(this);
      obj.action_arg._e = e;

      //コールバックの実行
      obj.action(obj.action_arg);

      // カスタムイベントを発火
      if (obj.event) {
        $(window).trigger(obj.event);
      }

      // イベントリスナを外す
      $(this).off(end);

    });

    $(this).on( move ,function(e){
      $(this).off(end);
    });

  });
}



function toggleClass(obj){

  if( $(obj.target).hasClass(obj.className) ){
    $(obj.target).removeClass(obj.className);
  }else {
    $(obj.target).addClass(obj.className);
  }

}
