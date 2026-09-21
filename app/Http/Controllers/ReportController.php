<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function show(string $scan)
    {
        $s = DB::table('scans')->where('id', $scan)->first();
        if (!$s || !in_array($s->status, ['success','partial'])) abort(404);
        $checks = DB::table('checks')->where('scan_id', $scan)->get();
        $evidence = DB::table('evidence')->where('scan_id', $scan)->get();
        $pages = DB::table('pages')->where('scan_id', $scan)->get();
        return view('report.show', compact('s','checks','evidence','pages'));
    }

    public function csv(string $scan)
    {
        $s = DB::table('scans')->where('id', $scan)->first();
        if (!$s) abort(404);
        $checks = DB::table('checks')->where('scan_id', $scan)->get();
        return response()->streamDownload(function () use ($checks) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['check_id','tool','category','severity','status','weight','message','provenance']);
            foreach ($checks as $c) {
                fputcsv($out, [$c->check_id,$c->tool,$c->category,$c->severity,$c->status,$c->weight,$c->message,$c->provenance]);
            }
            fclose($out);
        }, 'report-'.$scan.'.csv', ['Content-Type' => 'text/csv']);
    }
}
