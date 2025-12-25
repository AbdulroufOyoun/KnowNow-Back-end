<?php

namespace App\Http\Middleware;

use App\Models\CollectionCode;
use App\Models\CourseCode;
use App\Models\CourseCollection;
use App\Models\CourseContain;
use App\Models\UserCode;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Subscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (\Auth::check() && \Auth::user()->roles->count() > 0) {
            return $next($request);
        }
        $courseContains = CourseContain::where('video', $playlist = $request->route('playlist'))->first();

        $pdf = $request->route('pdf');
        if ($pdf) {
            $courseContains = CourseContain::where('pdf', $pdf)->first();
        } else {
            $playlist = $request->route('playlist');
            $courseContains = CourseContain::where('video', $playlist)->first();
        }


        // return $courseContains;
        // Check if course content exists
        if (!$courseContains) {
            return response()->json([
                'success' => false,
                'message' => 'Course content not found',
                'code' => 404,
            ], 404);
        }
        // If content is free, allow access
        if ($courseContains->is_free) {
            return $next($request);
        }

        // Check user subscription for paid content
        $courseCodes = CourseCode::onlyTrashed()->where('course_id', $courseContains->course_id)->where('expire_at', '>', Carbon::now())->pluck('id');
        $courseCollections = CourseCollection::where('course_id', $courseContains->course_id)->pluck('collection_id');
        $collectionCodes = CollectionCode::onlyTrashed()->whereIn('collection_id', $courseCollections)->pluck('id');

        $userCodes = UserCode::where('user_id', \Auth::user()->id)->where(function ($query) use ($courseCodes, $collectionCodes) {
            $query->whereIn('course_code_id', $courseCodes)
                ->orWhereIn('collection_code_id', $collectionCodes);
        })->first();

        if ($userCodes) {
            return $next($request);
        } else {
            return \Success('لست مشترك بهذه الدورة', false);
        }
    }
}
