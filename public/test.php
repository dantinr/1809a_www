<?php
function find2num($arr,$sum)
{
    $len = count($arr);
    for($j=0;$j<$len;$j++){
        $minus = $sum - $arr[$j];
        unset($arr[$j]);
        $two = array_search($minus,$arr);
        if( $two ){
            echo '数组的索引值为['.$j.','.$two.']';die;
        }
    }
}
$arr = [1,3,5,7,9];
find2num($arr,8);
