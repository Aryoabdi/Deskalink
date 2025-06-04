<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Service;
use App\Models\Report;
use App\Models\ModerationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    /**
     * Display content moderation dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pending_designs = Design::with('partner')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'designs');

        $pending_services = Service::with('partner')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'services');

        return view('admin.moderation.index', compact('pending_designs', 'pending_services'));
    }

    /**
     * Display moderation logs.
     *
     * @return \Illuminate\View\View
     */
    public function logs()
    {
        $logs = ModerationLog::with(['moderator'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.moderation.logs', compact('logs'));
    }

    /**
     * Display user reports.
     *
     * @return \Illuminate\View\View
     */
    public function reports()
    {
        $reports = Report::with(['reporter', 'reportedUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.moderation.reports', compact('reports'));
    }

    /**
     * Handle content moderation action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $type
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function moderate(Request $request, $type, $id)
    {
        $request->validate([
            'action' => ['required', 'in:approved,rejected,banned'],
            'reason' => ['required_if:action,rejected,banned', 'nullable', 'string', 'max:255'],
        ]);

        try {
            DB::beginTransaction();

            $content = null;
            if ($type === 'design') {
                $content = Design::findOrFail($id);
            } elseif ($type === 'service') {
                $content = Service::findOrFail($id);
            } else {
                throw new \Exception('Invalid content type');
            }

            // Update content status
            $content->update(['status' => $request->action]);

            // Create moderation log
            ModerationLog::create([
                'content_id' => $id,
                'content_type' => $type,
                'moderator_id' => auth()->user()->user_id,
                'action' => $request->action,
                'reason' => $request->reason
            ]);

            // If content is banned, also update partner status if needed
            if ($request->action === 'banned') {
                $partner = $content->partner;
                $banned_count = Design::where('partner_id', $partner->user_id)
                    ->where('status', 'banned')
                    ->count() +
                    Service::where('partner_id', $partner->user_id)
                    ->where('status', 'banned')
                    ->count();

                // If partner has more than 3 banned content, suspend their account
                if ($banned_count >= 3 && $partner->status === 'active') {
                    $partner->update(['status' => 'suspended']);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Content moderated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error moderating content: ' . $e->getMessage());
        }
    }

    /**
     * Handle user report action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleReport(Request $request, Report $report)
    {
        $request->validate([
            'action' => ['required', 'in:resolved,dismissed'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::beginTransaction();

            $report->update([
                'status' => 'resolved',
                'resolution_note' => $request->note,
                'resolved_at' => now(),
                'resolved_by' => auth()->user()->user_id
            ]);

            // If action is to suspend/ban the reported user
            if ($request->has('user_action')) {
                $reported_user = $report->reportedUser;
                $reported_user->update(['status' => $request->user_action]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Report handled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error handling report: ' . $e->getMessage());
        }
    }
}
