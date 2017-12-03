<?php
error_reporting(E_ALL & ~E_NOTICE);
require_once 'config/config.php';
require_once 'class/databaseAction.php';
require_once 'func/functions.php';
$action = new databaseAction();

// リクエストパラメータの取得
$category = $_GET["category"];
$value = $_GET["value"];

// タグ付けデータを取得
$attachedTags = $action->getTaggedData();
// タグリストを取得
$tagList = $action->getTagList();

// データを取得
if ($category) {
  // カテゴリー名で分岐
  switch ($category) {
    case 'member':
      // 関連人物の検索
      $members = $action->getMemberData($category,$value);
      $thePerson = $members[0];
      $title = $thePerson["family_name"].$thePerson["first_name"]."の関連人物";
      break;
    case 'tag':
      // タグ検索
      $members = $action->getMemberData($category,$value);
      $title = $tagList[$value - 1]["tag_name"];
      break;
    default:
      // キーワードを含む詳細検索
      $members = $action->getMemberData($category,$value);
      $title = $value."の検索結果";
      break;
  }
}else {
  // 全メンバーデータを取得
  $members = $action->getMemberData(null,null);
}

if(empty($members)){
  $title = "検索結果がありません";
}


?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <?php if ($title): ?>
      <title><?= $title ?>｜OBOERU PROJECT</title>
    <?php else: ?>
      <title>OBOERU PROJECT</title>
    <?php endif; ?>
    <meta name="description" content="そうだ、おぼぷろで調べよう">
    <meta name="viewport" content="width=500, user-scalable=no">
    <style media="screen">
    <?php if (empty($category)): ?>
    #header{
      transform: translateY(-100%);
    }
    <?php endif; ?>
    </style>
    <!-- CDN -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:200,300,400" rel="stylesheet">
    <link href="https://fonts.googleapis.com/earlyaccess/roundedmplus1c.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.20.3/TweenMax.min.js"></script>
    <!-- リソース -->
    <link rel="stylesheet" href="css/normarize.css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="css/style.css" media="screen" title="no title" charset="utf-8">
    <script src="js/functions.js" charset="utf-8"></script>
    <!-- アナリティクス -->
    <?php include_once("analyticstracking.php") ?>
  </head>
  <body>

    <!-- ヘッダーメニュー -->
    <header id="header">
      <form id="header_search_box" action="index.php" method="get">
        <input type="hidden" name="category" value="name">
        <input id="text_box" type="text" placeholder="<?= $placeholder ?>" name="value" value="">
        <!-- <button id="search_button" type="submit">
          <i class="fa fa-search search-icon" aria-hidden="true"></i>
        </button> -->
        <button id="header_left_btn" type="button">
          <!-- <span class="tips">漢字、ひらがな、アルファベットで名前検索</span> -->
          <i class="fa fa-search search-icon" aria-hidden="true"></i>
        </button>
      </form>
      <h1 class="header-title"><a class="title-link" href="?">OBOERU PROJECT</a></h1>
      <button id="header_right_btn"><i class="fa fa-bars" aria-hidden="true"></i></button>
    </header>

    <div class="loader">Loading...</div>

    <!-- 黒背景 -->
    <div id="black_layer">
    </div>

    <!-- ドロワーメニュー -->
    <div id="drawer_menu_wrapper">
      <ul id="drawer_menu">
        <?php
        foreach ($tagList as $tag) {
         echo "<a class='drawer_menu_item' href='?category=tag&value=",$tag["tag_id"],"'>",$tag["tag_name"],"</a>";
        } ?>
      </ul>
    </div>


    <!-- ラッパー -->
    <div id="main_wrapper">

      <!-- メインビジュアル・タイトル・検索ボックス -->
      <?php if (empty($category)): ?>
        <div class="top-visual">
          <div class="title-info">
            <!-- <img src="image/obopro_icon.svg"> -->
            <div class="top-title">
              <span class="a1">
                <span class="a2">
                  <span>O</span>
                  <span>B</span>
                  <span>O</span>
                  <span>P</span>
                  <span>R</span>
                  <span>O</span>
                </span>
              </span>
            </div>
            <div class="top-title-copy"><span class="a1"><span class="a2">Accelerating Collaboration</span></span></div>
          </div>

          <div id="search_box-wrapper">
            <form id="search_box" action="index.php" method="get">
              <input type="hidden" name="category" value="name">
              <input id="text_box" type="text" placeholder="<?= placeholderMaker($members); ?>" name="value" value="">
              <button id="search_button" type="submit">
                <i class="fa fa-search search-icon" aria-hidden="true"></i>
              </button>
            </form>
          </div>

          <div id="tag_primary">
            <a href='?category=tag&value=71'>神楽</a>
            <a href='?category=tag&value=72'>ZOO</a>
            <a href='?category=tag&value=73'>SCOPE</a>
            <a href='?category=tag&value=74'>SHICARON</a>
          </div>

          <div class="wave"></div>
          <div class="wave"></div>
          <div class="wave"></div>

          <!-- <a id="scroll_instraction" href="#main"><span></span>View All</a> -->

        </div>
      <?php endif; ?>


      <!-- サブタイトル -->
      <?php if ($title): ?>
        <h2 id="title"><span><?= $title ?></span></h2>
      <?php endif; ?>


      <!-- アイコン -->
      <?php if ($thePerson): ?>
        <div class="icon related-icon-top" style="background-image:url('images/<?= $thePerson["team"]; ?>/<?= $thePerson["img"]; ?>')"></div>
      <?php endif; ?>

<?php if (!empty($category)): ?>

      <!-- プロフィール一覧メインエリア -->
      <ul id="main" class="main clearfix">
<?php foreach ($members as $member) { ?>

<!-- 関連人物検索の場合は当該人物はスルー -->
<?php if ( $member["id"] == $thePerson["id"] ) { continue; } ?>


        <!-- 学籍IDを変数へ格納 -->
        <?php $college = in_array($member["college"], array('SSS','PSE','CMS','SOC','SILS','CSE','EDU','LAW','FSE','ASE','HUM','HSS','SPS')) ? $member["college"] : 'OTH' ; ?>

        <!-- ループテンプレート -->
        <li id="id<?= $member["id"]; ?>" class="singlefigure">
          <div class="unfolder">

            <!-- 学籍アイコン -->
            <a class="college" href="?category=college&value=<?= $college ?>"><?= $college ?></a>

            <!-- 写真 -->
            <div class="icon" data-img-src='images/<?= $member["team"]; ?>/<?= $member["img"]; ?>'>
              <?php if ($member["RelationRate"]): ?>
              <div class="RelationRate">
                <?= $member["RelationRate"]; ?>
              </div>
              <?php endif; ?>
              <i class="fa fa-times close" aria-hidden="true"></i>
            </div>

            <!-- モットー -->
            <div class="motto"><?= $member["motto"]; ?></div>

            <!-- 名前 -->
            <h4 class="name"><?= $member["family_name"]," ",$member["first_name"]; ?></h4>
            <p class="name-alphabet"><?= $member["family_name_alphabet"]," ",$member["first_name_alphabet"]; ?></p>

            <!-- タグエリア -->
            <div class="tag-area-wrapper">

              <div class="tag-area">

                <!-- つけられているタグを表示 -->
                <?php foreach ($attachedTags as $attachedTag): ?>

                      <?php if ($attachedTag["member_id"] === $member["id"]): ?>

                        <a class='tag' href='?category=tag&value=<?= $attachedTag["tag_id"]; ?>'><?= $attachedTag["tag_name"]; ?></a>

                      <?php endif; ?>

                <?php endforeach; ?>

              </div> <!--tag-area -->

            </div> <!--tag-area-wrapper -->

            <!-- 関連メンバーを探す -->
            <div class="relative-user">
              <a href='?category=member&value=<?= $member["id"]; ?>'>関連人物を見る</a>
            </div>


          </div> <!--unfolder -->

        </li> <!--singlefigure -->

<?php } ?>

      </ul> <!--main -->

<?php endif; ?>

      <div class="footer"><p>Produced By PublicRelationsSection</p></div>



    </div> <!-- wrapper -->

    <script type="text/javascript">

      $(document).ready(function() {
        searchBox();
        readyAnimation();

        touchEventRegister({
          "target": $('#header_right_btn'),
          "action": toggleClass,
          "action_arg": {
            "target": $('body'),
            "className": "draw"
          },
          "event":"draw"
        });

        touchEventRegister({
          "target": $('#black_layer'),
          "action": toggleClass,
          "action_arg": {
            "target": $('body'),
            "className": "draw"
          },
          "event":"draw"
        });

        touchEventRegister({
          "target": $('.unfolder'),
          "action": switchActivation,
          "action_arg": {}
        });

        <?php if (empty($category)): ?>
        openingAnimation();
        topPageHeader();
        hrefScroll();
        <?php else: ?>
        scrollAnimation();
        <?php endif; ?>

      });

    </script>
  </body>
</html>
