<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 17.11.2014
 * Time: 14:05
 */

namespace samson\upload;


interface iAdapter {
    public function putContent($data, $filename = '');
}
