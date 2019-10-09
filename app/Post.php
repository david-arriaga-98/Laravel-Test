<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    public function user(){
        $this->belogsTo('App\User', 'user_id');
    }
    public function category(){
        $this->belogsTo('App\Category', 'category_id');
    }
}
