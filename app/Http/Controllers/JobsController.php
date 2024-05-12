<?php

namespace App\Http\Controllers;

use App\Mail\JobNotificationEmail;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SavedJob;
use App\Models\JobType;
use App\Models\Job;
use App\Models\User;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class JobsController extends Controller
{
    //pour la page des Jobs
    public function index(Request $request){
        $categories=Category::where('status',1)->get();
        $jobTypes=JobType::where('status',1)->get();

        $jobs=Job::where('status',1);
        //recherech avec des element clé
        if(!empty($request->keyword)){
            $jobs=$jobs->where(function($query) use($request){
                $query->orWhere('title','like','%'.$request->keyword.'%');
                $query->orWhere('keywords','like','%'.$request->keyword.'%');
            });
        }

        //recherch avec location
        if(!empty($request->keyword)){
            $jobs=$jobs->where('location',$request->location);
        }

        //recherch avec category
        if(!empty($request->category)){
            $jobs=$jobs->where('category_id',$request->category);
        }

        //recherch avec type de job
        $jobTypeArray=[];
        if(!empty($request->jobType)){
            $jobTypeArray=explode(',',$request->jobType);

            $jobs=$jobs->where('job_type_id',$jobTypeArray);
        }

        //recherch avec experience
        if(!empty($request->experience)){
            $jobs=$jobs->where('experience',$request->experience);
        }


        $jobs=$jobs->with(['jobType','category']);
        if($request->sort=='0'){
            $jobs=$jobs->orderBy('created_at','ASC');
        }else{
            $jobs=$jobs->orderBy('created_at','DESC');
        }
        $jobs=$jobs->paginate(9);

        return view('front.jobs',[
            'categories'=>$categories,
            'jobTypes'=>$jobTypes,
            'jobs'=>$jobs,
            'jobTypeArray'=>$jobTypeArray
        ]);
    }

    public function detail($id){
        $job= Job::where(['id'=>$id,'status'=>1])->with(['jobType','category'])->first();
        if($job==null){

        }
        $count=0;
        if(Auth::user()){
            $count= SavedJob::where([
                'user_id'=>Auth::user()->id,
                'job_id'=>$id
            ])->count();
        }

        $applications= JobApplication::where('job_id',$id)->with('user')->get();


        return view('front.jobDetail',['job'=>$job,'count'=>$count,'applications'=>$applications]);
    }

    public function applyJob(Request $request) {
        $id = $request->id;

        $job = Job::where('id',$id)->first();

        if ($job == null) {
            $message = 'Job does not exist.';
            session()->flash('error',$message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        $employer_id = $job->user_id;

        if ($employer_id == Auth::user()->id) {
            $message = 'You can not apply on your own job.';
            session()->flash('error',$message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }


        $jobApplicationCount = JobApplication::where([
            'user_id' => Auth::user()->id,
            'job_id' => $id
        ])->count();

        if ($jobApplicationCount > 0) {
            $message = 'You already applied on this job.';
            session()->flash('error',$message);
            return response()->json([
                'status' => false,
                'message' => $message
            ]);
        }

        $application = new JobApplication();
        $application->job_id = $id;
        $application->user_id = Auth::user()->id;
        $application->employer_id = $employer_id;
        $application->applied_date = now();
        $application->save();

        $employer=User::where('id',$employer_id)->first();
        $mailData=[
            'employer'=>$employer,
            'user'=>Auth::user(),
            'job'=>$job
        ];

        Mail::to($employer->email)->send(new JobNotificationEmail($mailData));


        $message = 'You have successfully applied.';

        session()->flash('success',$message);

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function saveJob(Request $request){
        $id=$request->id;
        $job=Job::find($id);
        if($job==null){
            session()->flash('error','Job Not Found');
            return response()->json([
                'status'=>false,
            ]);
        }

        $count= SavedJob::where([
            'user_id'=>Auth::user()->id,
            'job_id'=>$id
        ])->count();

        if($count>0){
            session()->flash('error','You Already Saved This Job');
            return response()->json([
                'status'=>false,
            ]);
        }

        $savedJob= new SavedJob;
        $savedJob->job_id=$id;
        $savedJob->user_id=Auth::user()->id;
        $savedJob->save();

        session()->flash('success','You Have Successfully Saved The Job');
            return response()->json([
                'status'=>true,
            ]);
    }



}
