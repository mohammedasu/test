<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index()
	{
		$book = Book::all();
		
		if( $book->isNotEmpty() )
		{
			foreach($book as $key => $value)
			{
				$value->cover_image = Storage::url($value->cover_image);
			}
		}
		
		return response()->json(['success' => true, 'data' => $book],200);
	}
	
	public function show($id)
	{
		$book = Book::find($id);
		
		if( !is_null($book) )
		{
			$book->cover_image = Storage::url($book->cover_image);
		}
		return response()->json(['success' => true, 'data' => $book],200);
	}
	
	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'book_name' 	=> 'required|string|max:255',
			'author'		=> 'required|string|max:255',
			'cover_image'	=> 'mimes:jpeg,jpg,png,gif|required|max:10000',
		]);

		if($validator->fails()){
			return response()->json(['error' => true, 'message' => $validator->errors()->all()], 400);
		}
		
		$cover_image = $this->uploadFile($request->file('cover_image'));
		
		$book = Book::create([
			'book_name' 	=> $request->get('book_name'),
			'author' 		=> $request->get('author'),
			'cover_image' 	=> $cover_image,
		]);
		
		$book->cover_image = Storage::url($cover_image);

		return response()->json(compact('book'),200);
	}
	
	public function uploadFile($image)
	{
		return Storage::putFile('cover_image',$image);
	}
	
	public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
			'book_name' 	=> 'required|string|max:255',
			'author'		=> 'required|string|max:255',
		]);

		if($validator->fails()){
			return response()->json(['error' => true, 'message' => $validator->errors()->all()], 400);
		}
		
		$checkRecord = Book::where('id',$id)->first();
		if( is_null($checkRecord) )
        {
            return response()->json(['error' => true, 'message' => 'Record not exist!'], 404);
        }
		
		$cover_image = $checkRecord->cover_image;
		if( isset($request->cover_image) )
		{
			if( !empty($request->file('cover_image')) )
			{
				Storage::delete($checkRecord->cover_image);
				$cover_image = $this->uploadFile($request->file('cover_image'));
			}
		}
		
		$data = $checkRecord->update([
			'book_name' 	=> $request->get('book_name'),
			'author' 		=> $request->get('author'),
			'cover_image' 	=> $cover_image,
		]);
		
        return response()->json(['success' => true, 'message' => 'Book updated successfully', 'data' => $data],200);
    }
	
	public function destroy($id)
    {
        $checkRecord = Book::where('id',$id)->first();
		if( is_null($checkRecord) )
        {
            return response()->json(['error' => true, 'message' => 'Record not exist!'], 404);
        }
		
		if( !is_null($checkRecord->cover_image) )
		{
			Storage::delete($checkRecord->cover_image);
		}
		
		$data = $checkRecord->delete();
		
        return response()->json(['success' => true, 'message' => 'Book deleted successfully', 'data' => $data],200);
    }
}
