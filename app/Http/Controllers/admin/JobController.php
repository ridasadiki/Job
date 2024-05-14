<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobType;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(){
        $jobs=Job::orderBy('created_at','DESC')->with('user','applications')->paginate(10);
        return view('admin.jobs.list',[
            'jobs'=>$jobs
        ]);
    }
    public function edit($id){
        $job=Job::findOrFail($id);
        $categories=Category::orderBy('name','ASC')->get();
        $jobTypes=JobType::orderBy('name','ASC')->get();
        return view('admin.jobs.edit',[
            'job'=>$job,
            'categories'=>$categories,
            'jobTypes'=>$jobTypes
        ]);
    }
    public function update(Request $request,$id){
        $rules = [
            'title' => 'required|min:5|max:200',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required|integer',
            'location' => 'required|max:50',
            'description' => 'required',
            'company_name' => 'required|min:3|max:75',

        ];
        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()) {

            $job = Job::find($id);
            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->job_type_id  = $request->jobType;
            $job->user_id = Auth::user()->id;
            $job->vacancy = $request->vacancy;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->responsibility = $request->responsibility;
            $job->qualifications = $request->qualifications;
            $job->keywords = $request->keywords;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_location = $request->company_location;
            $job->company_website = $request->website;
            $job->save();

            session()->flash('success','Job updated successfully.');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);


        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}
