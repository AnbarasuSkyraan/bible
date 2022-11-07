<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin;
use App\Models\Version;
use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Support\Facades\Auth;    
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
    function loginpost(Request $request){
         //Validate Inputs
         $request->validate([
            'email'=>'required|email|exists:admins,email',
            'password'=>'required|min:5|max:30'
         ],[
             'email.exists'=>'This email is not exists in admins table'
         ]);

         $creds = $request->only('email','password');

         if( Auth::guard('admin')->attempt($creds) ){
             return redirect()->route('admin.addversions');
         }else{
             return redirect()->route('admin.login')->with('fail','Incorrect credentials');
         }
    }

    function logout(){
        Auth::guard('admin')->logout();
        return redirect('/');
    }
    public function dashboard(Request $request)
    {
        return view('dashboard.admin.home');
    }


    //Version Crud Operations
    public function AddVersions(Request $request)
    {
        return view('admin.versions.add');
    }
    public function AddChapter(Request $request)
    {
        $versions=Version::all();
        $books=Book::all();
        return view('admin.chapters.add',['versions' => $versions,'books' => $books]);
    }   
    public function AddChapterPost(Request $request)
    {
        $request->validate([
            'version'=>'required',
            'book_name'=>'required',
            'chapter_name'=>'required', 
            'status' => 'required',
            'meta_keyword' => 'required',
            'meta_description' => 'required',
         ],[
             'version.required'=>'Version Name is Required',
             'status.required'=>'Status is Required',
             'book_name' => 'Book Name Is Required',
             'chapter_name' => 'Chapter Name Is Required',
             'meta_keyword.required'=>'Meta Keyword is Required',
             'meta_description.required'=>'Meta Description is Required',
         ]);
         $chapters=new Chapter();
         $chapters->version_id=$request->version;
         $chapters->book_id=$request->book_name;
         $chapters->chapter_name=$request->chapter_name;
         $chapters->meta_keyword=$request->meta_keyword;
         $chapters->meta_description=$request->meta_description;
         $chapters->status=$request->status;
         if($chapters->save())
         {
            return redirect()->back()->with('success', 'Sucessfully Added');  
         }
         else
         {
            return redirect()->back()->with('error', 'Problem on Insert');  
         }
    }
    public function AddVersionsPost(Request $request)
    {
        
        $request->validate([
            'version_name'=>'required',
            'status'=>'required',
            'meta_keyword'=>'required', 
            'meta_description'=>'required',
         ],[
             'version_name.required'=>'Version Name is Required',
             'status.required'=>'Status is Required',
             'meta_keyword.required'=>'Meta Keyword is Required',
             'meta_description.required'=>'Meta Description is Required',
         ]);
         $version=new Version();
         $version->version_name=$request->version_name;
         $version->status=$request->status;
         $version->metakeywords=$request->meta_keyword;
         $version->metadescription=$request->meta_description;
         $version->createdby=auth::user()->name;
         $version->updatedby=auth::user()->name;
         $version->createdid=auth::user()->id;
         $version->updatedid=auth::user()->id;
         $version->save();
         if($version->save())
         {
            return redirect()->back()->with('success', 'Sucessfully Added');  
         }
         else
         {
            return redirect()->back()->with('error', 'Problem on Insert');  
         }
    }
    public function UpdateBooksPost(Request $request)
    {
        $request->validate([
            'version'=>'required',
            'book_title'=>'required', 
            'book_short_title'=>'required',
            'meta_keyword'=>'required',
            'meta_description'=>'required',
         ],[
             'version.required'=>'Version Name is Required',
             'book_title.required'=>'Book Title is Required',
             'book_short_title.required'=>'Book Short Title is Required',
             'meta_keyword.required'=>'Meta Description is Required',
             'meta_description.required'=>'Meta Description is Required',
         ]);
         $updatebooks=Book::where('id',$request->id)->update(['version_id' => $request->version,'title' => $request->book_title,'short_title' => $request->book_short_title,'meta_keyword' => $request->meta_keyword,'meta_description' => $request->meta_description]);
         if($updatebooks)
         {
            return redirect()->back()->with('success', 'Sucessfully Updated');  
         }
         else
         {
            return redirect()->back()->with('error', 'Problem on update');  
         }
    }
    public function UpdateVersionPost(Request $request)
    {
        
        $request->validate([
            'version_name'=>'required',
            'status'=>'required',
            'meta_keyword'=>'required', 
            'meta_description'=>'required',
         ],[
             'version_name.required'=>'Version Name is Required',
             'status.required'=>'Status is Required',
             'meta_keyword.required'=>'Meta Keyword is Required',
             'meta_description.required'=>'Meta Description is Required',
         ]);
         $updateversion=Version::where('id',$request->id)->update(['version_name' => $request->version_name,'status' => $request->status,'metakeywords' => $request->meta_keyword,'metadescription' => $request->meta_description]);
      
         if($updateversion)
         {
            return redirect()->back()->with('success', 'Sucessfully Updated');  
         }
         else
         {
            return redirect()->back()->with('error', 'Problem on update');  
         }
          
         
    }
    public function ManageBooks(Request $request,$pagenumber)
    {
        $noofrecords=10;
        $bookscount=Book::count();
        if($noofrecords > 10)
        {
            $pagescount=($bookscount/$noofrecords);
            $pagescount+=($bookscount%$noofrecords > 0) ? 1 : 0;
        }
        else
        {
            $pagescount=$bookscount == 0 ? 0 : 1;
        }
        $start=(($pagenumber-1)*10);
        $books = Book::offset($start)->limit(10)->get();  
       return view('admin.books.manage',['books' => $books,'bookscount'=> $bookscount,'pagescount' => $pagescount,'noofrecords' => $noofrecords,'pagenumber' => $pagenumber]);
    }
    public function ManageChapters(Request $request,$pagenumber=1)
    {
        $noofrecords=10;
        $versionsCount=Version::count();
        if($noofrecords > 10)
        {
            $pagescount=($versionsCount/$noofrecords);
            $pagescount+=($versionsCount%$noofrecords > 0) ? 1 : 0;
        }
        else
        {
            $pagescount=$versionsCount == 0 ? 0 : 1;
        }
        $start=(($pagenumber-1)*10);
        $end=$start+10;
        $chapters = DB::table('chapters')
        ->join('versions', 'chapters.version_id', '=', 'versions.id')
        ->join('books', 'chapters.book_id', '=', 'books.id')
        ->select('chapters.id as id','versions.version_name as version_name','versions.id as version_id','books.id as book_id','books.title as book_name','chapters.chapter_name as chapter_name', 'chapters.status as status','chapters.meta_keyword as meta_keyword','chapters.meta_description as meta_description')
        ->get();
    
        return view('admin.chapters.manage',['chapters' => $chapters,'versionscount'=> $versionsCount,'pagescount' => $pagescount,'noofrecords' => $noofrecords,'pagenumber' => $pagenumber]);
    }
    public function ManageVersion(Request $request,$pagenumber)
    {
        $noofrecords=10;
        $versionsCount=Version::count();
        if($noofrecords > 10)
        {
            $pagescount=($versionsCount/$noofrecords);
            $pagescount+=($versionsCount%$noofrecords > 0) ? 1 : 0;
        }
        else
        {
            $pagescount=$versionsCount == 0 ? 0 : 1;
        }
        $start=(($pagenumber-1)*10);
        $end=$start+10;
        $versions = Version::offset($start)->limit(10)->get();  
       
       return view('admin.versions.manage',['versions' => $versions,'versionscount'=> $versionsCount,'pagescount' => $pagescount,'noofrecords' => $noofrecords,'pagenumber' => $pagenumber]);
    }
    public function getVersions(Request $request,$pagenumber)
    {
        $data=array();
        $start=(($pagenumber-1)*10)+1;
        $end=$start+10;
        $versions = Version::limit($start,$end)->get();     
        $data['success']=true;
        $data['versions']=$versions;
        return response()->json($data);
    }
    public function DeleteVersionRecord(Request $request)
    {
        $data=array();
        $deleted=Version::where('id',$request->id)->delete();
        if($deleted == 1)
        {
            $data['success']=true;
        }
        else
        {
            $data['success']=false;
        }
        return response()->json($data);
    }
    public function EditVersions(Request $request,$id)
    {
        $editversions=Version::where('id',$request->id)->get();
       return view('admin.versions.edit',['editversions' => $editversions,'id' => $id]);
    }
    public function EditBooks(Request $request,$id)
    {
        $versions=Version::all();
        $editbooks=Book::where('id',$request->id)->get();
       return view('admin.books.edit',['editbooks' => $editbooks,'id' => $id,'versions' => $versions]);
    }
    public function AddRecords(Request $request)
    {
       
        return view('admin.records.add');
    }
    public function AddBooks(Request $request)
    {
        
        $versions=Version::all();
        return view('admin.books.add',['versions' => $versions]);
    }
    public function AddBooksPost(Request $request)
    {
        $request->validate([
            'version'=>'required',
            'book_title'=>'required', 
            'book_short_title'=>'required',
            'meta_keyword'=>'required',
            'meta_description'=>'required',
         ],[
             'version.required'=>'Version Name is Required',
             'book_title.required'=>'Book Title is Required',
             'book_short_title.required'=>'Book Short Title is Required',
             'meta_keyword.required'=>'Meta Description is Required',
             'meta_description.required'=>'Meta Description is Required',
         ]);
             $bookscount=Book::count();
            if($bookscount > 0)
            {
                $lastvalue = Book::orderByDesc('book_num')->limit(1)->get()[0]['book_num'];
            }
            else
            {
                $lastvalue=1;
            }
         $books=new Book();
         $books->version_id=$request->version;
         $books->book_num=$lastvalue;
         $books->title=$request->book_title;
         $books->short_title=$request->short_title;
         $books->meta_keyword=$request->meta_keyword;
         $books->meta_description=$request->meta_description;
         if($books->save())
         {
            return redirect()->back()->with('success', 'Sucessfully Added');  
         }
         else
         {
            return redirect()->back()->with('error', 'Problem on Insert');  
         }
    }
    public function PickBooks(Request $request)
    {
        $result=Book::where('version_id',$request->id)->get();
        return response()->json(array('success' => true, 'data' => $result));
    }
    public function getprayerdata(Request $request)
    {
        $result=Book::get();
        return response()->json(array('Result ' => 1,'error ' => 0,  'data' => $result));
    }
}
