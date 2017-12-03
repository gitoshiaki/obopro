<?php

function placeholderMaker($members){
  if(!empty($members)) {
    $max = count($members);
    $randomNum = rand(1,$max);
    $placeholder = $members[$randomNum - 1]["family_name"].$members[$randomNum - 1]["first_name"]."を検索";
    return $placeholder;
  }
}
