<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $type    = $request->query('type');
        $allowed = ['auth', 'post', 'blog', 'user', 'admin', 'subscription', 'ai','plan'];

        $logs = ActivityLog::with('user')
            ->when(in_array($type, $allowed, true), fn ($q) => $q->where('action', 'like', $type . '.%'))
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.activity', compact('logs', 'type'));
    }
}