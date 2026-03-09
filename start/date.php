<?php
echo date('Y年m月d日 H:i:s');
echo '<br>';
date_default_timezone_set('Asia/Tokyo');
echo date('Y年m月d日 H:i:s');
echo '<br>';
// DBからもってきた体裁づくり
$target_time = '2026-03-09 10:23:54';

// 日付の文字列をUNIXタイムスタンプに変換する
$timestamp = strtotime($target_time);

echo $timestamp;
echo '<br>';
echo date('Y年m月d日 H:i:s', strtotime($target_time));
