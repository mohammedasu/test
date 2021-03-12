<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RentBook extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','book_id','is_rentel'
    ];

	public function user()
	{
		return $this->belongsTo(User::class)->select('firstname','lastname');
	}
	
	public function book()
	{
		return $this->belongsTo(Book::class)->select('book_name','author');
	}

}
