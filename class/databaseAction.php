<?php


class databaseAction {

    public $pdo;



    /**
     * コネクション確保
     */
    function __construct() {
        try {
            $this->pdo = new PDO( PDO_DSN, DATABASE_USER, DATABASE_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'error' . $e->getMessage();
            die();
        }
    }



    /**
     * メンバーデータをDBから読み込み
     */
    public function getMemberData($category = null, $value = null){

      if ($category) {
        switch ($category) {

          case 'name':
            return $this->searchByKeyword($value);

          case 'member':
            return $this->getRelated($value);

          case 'tag':
            return $this->searchByTag($value);

          default:
              return $this->searchByCategory($category, $value);

        } //switch
      } else {

        return $this->getAll();

      } //if
    } //getmemberdata




    /**
     * タグ付けデータをDBから読み込み
     */
    public function getTaggedData(){

      $sql = "SELECT * FROM matchings LEFT JOIN tags ON matchings.tag_id = tags.tag_id ";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;

    }


    /**
     * タグデータをDBから読み込み
     */
    public function getTagList(){

      $sql = "SELECT * FROM tags";
    	$stmt = $this->pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;

    }


    private function getAll(){

      $sql = "SELECT * FROM members ORDER BY family_name_kana ASC";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;

    }


    private function searchByKeyword($keyword){

      $keyword = '%'.$keyword.'%';
      $sql = "SELECT * FROM members WHERE concat(family_name,first_name,family_name_kana,first_name_kana,family_name_alphabet,first_name_alphabet) LIKE :keyword ORDER BY family_name_kana ASC ";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;

    }


    private function searchByTag($value){

      $sql = "SELECT * FROM members,matchings WHERE members.id = matchings.member_id AND matchings.tag_id = :tag_id ORDER BY family_name_kana ASC ";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindParam(':tag_id', $value, PDO::PARAM_INT);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;

    }


    private function searchByCategory($category, $value){

      $sql = "SELECT * FROM members WHERE members.$category = :value ORDER BY family_name_kana ASC ";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindParam(':value', $value, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result;

    }


    private function getRelated($value){

      // タグ付けデータを取得
      $attachedTags = $this->getTaggedData();

      //タグ付けデータ全体をループ処理（1）
      foreach ($attachedTags as $attachedTag1) {

        //指定したメンバーのタグ付けデータの場合
        if ($attachedTag1["member_id"] == $value){

          //タグ付けデータ全体をループ処理（2）
          foreach ($attachedTags as $attachedTag2) {

            //共通のタグをもっているタグ付けデータの場合
            if ($attachedTag2["tag_id"] == $attachedTag1["tag_id"]) {

              //関連メンバーと関連度を格納した配列を作成。
              $relatedMembers[$attachedTag2['member_id']] += 1;

            } //if
          } //foreach

        } //if
      } //foreach

      //関連度によって並び替え処理
      arsort($relatedMembers);

      // 全メンバーを取得
      $allMembers = $this->getMemberData(null,null);

      // 配列を初期化
      $SortedRelatedMembers = array();

      // ソート
      foreach ($relatedMembers as $member_id => $related_rate) {
        foreach ($allMembers as $member) {
          if ($member["id"] == $member_id && $member["id"] != $value ) {

            $member["RelationRate"] = $related_rate;
            $SortedRelatedMembers[] = $member;
            break;

          }elseif ($member["id"] == $value && $SortedRelatedMembers[0]["id"] != $value ) {

            array_unshift($SortedRelatedMembers,$member);

          }
        }
      }

      return $SortedRelatedMembers;

    }

}
