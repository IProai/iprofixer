<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRfqNoteRequest;
use App\Http\Requests\UpdateRfqStatusRequest;
use App\Models\FormSubmission;
use App\Models\RfqAttachment;
use App\Models\RfqNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class RfqController extends Controller
{
    private const STATUSES = ['new','qualified','in_progress','awaiting_client','closed_won','closed_lost'];

    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('rfq.manage'), 403);
        $filters = ['search'=>trim((string) $request->query('search','')),'status'=>(string) $request->query('status',''),'owner'=>(string) $request->query('owner','')];
        if (! in_array($filters['status'], self::STATUSES, true)) $filters['status']='';
        $activeUserIds = User::query()->where('is_active',true)->pluck('id')->map(static fn(mixed $id): string => (string) $id)->all();
        if ($filters['owner']!=='' && $filters['owner']!=='unassigned' && ! in_array($filters['owner'],$activeUserIds,true)) $filters['owner']='';
        $rfqs = FormSubmission::query()->where('type','rfq')->with('assignee')
            ->when($filters['search']!=='', function (Builder $query) use ($filters): void {
                $term='%'.str_replace(['%','_'],['\\%','\\_'],$filters['search']).'%';
                $operator=DB::connection()->getDriverName()==='pgsql'?'ilike':'like';
                $query->where(fn(Builder $q) => $q->where('reference',$operator,$term)->orWhere('contact_name',$operator,$term)->orWhere('organization_name',$operator,$term)->orWhere('email',$operator,$term));
            })->when($filters['status']!=='',fn(Builder $q)=>$q->where('status',$filters['status']))
            ->when($filters['owner']==='unassigned',fn(Builder $q)=>$q->whereNull('assigned_to'))
            ->when($filters['owner']!==''&&$filters['owner']!=='unassigned',fn(Builder $q)=>$q->where('assigned_to',$filters['owner']))
            ->latest('submitted_at')->paginate(25)->withQueryString();
        $assignees=User::query()->where('is_active',true)->orderBy('name')->get(['id','name']);
        return view('admin.rfqs.index',compact('rfqs','assignees','filters')+['statuses'=>self::STATUSES]);
    }

    public function show(Request $request, FormSubmission $rfq): View
    {
        abort_unless($request->user()?->can('rfq.manage'),403);
        abort_unless($rfq->type==='rfq',404);
        $rfq->load(['assignee','consents','attachments','notes.author']);
        $assignees=User::query()->where('is_active',true)->orderBy('name')->get(['id','name']);
        $activity=DB::table('audit_events')->where('subject_type',FormSubmission::class)->where('subject_id',(string)$rfq->getKey())->latest('occurred_at')->limit(50)->get();
        return view('admin.rfqs.show',compact('rfq','assignees','activity'));
    }

    public function storeNote(StoreRfqNoteRequest $request, FormSubmission $rfq): RedirectResponse
    {
        abort_unless($rfq->type==='rfq',404);
        $note=DB::transaction(function () use ($request,$rfq): RfqNote {
            $note=$rfq->notes()->create(['author_id'=>$request->user()->getKey(),'body'=>trim($request->validated('body'))]);
            DB::table('audit_events')->insert(['id'=>(string)Str::uuid(),'actor_id'=>$request->user()->getKey(),'action'=>'rfq.note.created','subject_type'=>FormSubmission::class,'subject_id'=>(string)$rfq->getKey(),'correlation_id'=>(string)Str::uuid(),'ip_address'=>$request->ip(),'user_agent'=>Str::limit((string)$request->userAgent(),1000),'before'=>null,'after'=>null,'metadata'=>json_encode(['note_id'=>(string)$note->getKey()],JSON_THROW_ON_ERROR),'occurred_at'=>now()]);
            return $note;
        });
        return back()->with('status','Internal note added.');
    }

    public function downloadAttachment(Request $request, FormSubmission $rfq, RfqAttachment $attachment): StreamedResponse
    {
        abort_unless($request->user()?->can('rfq.manage'),403); abort_unless($rfq->type==='rfq',404); abort_unless($attachment->form_submission_id===$rfq->getKey(),404);
        $disk=Storage::disk($attachment->disk); abort_unless($disk->exists($attachment->path),404);
        DB::table('audit_events')->insert(['id'=>(string)Str::uuid(),'actor_id'=>$request->user()->getKey(),'action'=>'rfq.attachment.downloaded','subject_type'=>RfqAttachment::class,'subject_id'=>(string)$attachment->getKey(),'correlation_id'=>(string)Str::uuid(),'ip_address'=>$request->ip(),'user_agent'=>Str::limit((string)$request->userAgent(),1000),'before'=>null,'after'=>null,'metadata'=>json_encode(['rfq_id'=>(string)$rfq->getKey(),'reference'=>$rfq->reference,'sha256'=>$attachment->sha256],JSON_THROW_ON_ERROR),'occurred_at'=>now()]);
        return $disk->download($attachment->path,$attachment->original_name,['Content-Type'=>$attachment->mime_type]);
    }

    public function update(UpdateRfqStatusRequest $request, FormSubmission $rfq): RedirectResponse
    {
        abort_unless($rfq->type==='rfq',404); $validated=$request->validated();
        DB::transaction(function () use ($request,$rfq,$validated): void {
            $before=['status'=>$rfq->status,'assigned_to'=>$rfq->assigned_to,'last_contacted_at'=>$rfq->last_contacted_at];
            $rfq->update(['status'=>$validated['status'],'assigned_to'=>$validated['assigned_to']??null,'last_contacted_at'=>($validated['mark_contacted']??false)?now():$rfq->last_contacted_at]);
            DB::table('audit_events')->insert(['id'=>(string)Str::uuid(),'actor_id'=>$request->user()->getKey(),'action'=>'rfq.workflow.updated','subject_type'=>FormSubmission::class,'subject_id'=>(string)$rfq->getKey(),'correlation_id'=>(string)Str::uuid(),'ip_address'=>$request->ip(),'user_agent'=>Str::limit((string)$request->userAgent(),1000),'before'=>json_encode($before,JSON_THROW_ON_ERROR),'after'=>json_encode(['status'=>$rfq->status,'assigned_to'=>$rfq->assigned_to,'last_contacted_at'=>$rfq->last_contacted_at],JSON_THROW_ON_ERROR),'metadata'=>json_encode(['source'=>'admin.rfq'],JSON_THROW_ON_ERROR),'occurred_at'=>now()]);
        });
        return back()->with('status','RFQ workflow updated.');
    }
}
