<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AnswerSample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnswerSampleController extends Controller
{
    public function index()
    {
        $completedAssessments = Assessment::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->with('subject')
            ->get();

        $recentUploads = Assessment::where('user_id', Auth::id())
            ->has('answerSamples')
            ->with(['subject', 'answerSamples'])
            ->get();

        // Make sure 'subjcoord' matches your folder name in resources/views
        return view('subjcoordinator.answersample-upload', compact('completedAssessments', 'recentUploads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
            'samples' => 'required|array|size:3', // Best, Medium, Weak
            'samples.*.*' => 'required|file|mimes:pdf|max:5120', // 5MB limit per PDF
        ]);

        $assessment = Assessment::findOrFail($request->assessment_id);

        foreach ($request->file('samples') as $category => $files) {
            foreach ($files as $file) {
                // Generate a clean filename: subject_type_category_timestamp.pdf
                $filename = strtolower($assessment->subject->subject_code) . '_' . 
                            strtolower($category) . '_' . 
                            time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                $path = $file->storeAs('answer_samples/' . strtolower($category), $filename, 'public');

                AnswerSample::create([
                    'assessment_id' => $assessment->id,
                    'category' => strtolower($category),
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->back()->with('success', 'All student samples uploaded successfully!');
    }
}