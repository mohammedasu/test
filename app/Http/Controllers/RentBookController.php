<?php

namespace App\Http\Controllers;

use App\RentBook;
use App\Book;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RentBookController extends Controller
{
    public function index()
	{
		$rent_book = RentBook::all()->where('is_rentel',1);
		
		if( $rent_book->isNotEmpty() )
		{
			foreach($rent_book as $key => $value)
			{
				$value->user = $value->user;
				$value->book = $value->book;
			}
		}
		
		return response()->json(['success' => true, 'data' => $rent_book],200);
	}
	
	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'user_id' 	=> 'required|integer',
			'book_id'	=> 'required|integer',
		]);

		if($validator->fails()){
			return response()->json(['error' => true, 'message' => $validator->errors()->all()], 400);
		}
		
		$check_record = $this->check_record($request->get('user_id'),$request->get('book_id'));
		if( !is_null($check_record) )
		{
			return $check_record;
		}
		
		$checkRecord = RentBook::where(array('user_id' => $request->get('user_id'), 'book_id' => $request->get('book_id'), 'is_rentel' => 1))->first();
		if( !is_null($checkRecord) )
        {
            return response()->json(['error' => true, 'message' => 'Already rented book'], 404);
        }
		
		
		$rent_book = RentBook::create([
			'user_id' 	=> $request->get('user_id'),
			'book_id' 	=> $request->get('book_id'),
			'is_rentel' => 1,
		]);
		
		return response()->json(compact('rent_book'),200);
	}
	
	public function return_book(Request $request)
    {
        $check_record = $this->check_record($request->get('user_id'),$request->get('book_id'));
		if( !is_null($check_record) )
		{
			return $check_record;
		}
		
		$checkRecord = RentBook::where(array('user_id' => $request->get('user_id'), 'book_id' => $request->get('book_id'), 'is_rentel' => 1))->first();
		if( is_null($checkRecord) )
        {
            return response()->json(['error' => true, 'message' => 'rented book not found'], 404);
        }
		
		$data = $checkRecord->update(['is_rentel' => 0]);
		
        return response()->json(['success' => true, 'message' => 'Book return successfully', 'data' => $data],200);
    }
	
	public function check_record($user_id,$book_id)
	{
		$checkUser = User::where('id',$user_id)->first();
		if( is_null($checkUser) )
        {
            return response()->json(['error' => true, 'message' => 'User not found!'], 404);
        }
		
		$checkBook = Book::where('id',$book_id)->first();
		if( is_null($checkBook) )
        {
            return response()->json(['error' => true, 'message' => 'Book not found!'], 404);
		}
	}
}
