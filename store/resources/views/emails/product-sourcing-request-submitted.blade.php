@extends('emails.layouts.modern', ['emailTitle' => 'New Procurement Request #'.$sourcingRequest->id, 'emailBadge' => 'Sourcing Desk', 'emailAccent' => '#7c3aed'])
@section('content')
<p style="margin:0 0 16px;color:#475569;">A customer submitted a volume-pricing or custom-sourcing request.</p>
@include('emails.partials.details', ['rows' => ['Customer' => e($sourcingRequest->user->name), 'Email' => '<a href="mailto:'.e($sourcingRequest->user->email).'" style="color:#2563eb">'.e($sourcingRequest->user->email).'</a>', 'Product / solution' => e($sourcingRequest->search_query), 'Manufacturer' => e($sourcingRequest->manufacturer ?: 'Not specified'), 'Model / part' => e($sourcingRequest->model_or_part_number ?: 'Not specified'), 'Quantity' => number_format($sourcingRequest->quantity), 'Submitted' => optional($sourcingRequest->created_at)->format('M j, Y g:i A T')]])
<div style="margin-top:18px;padding:16px;background:#fff;border:1px solid #dbe5f1;border-radius:10px;"><p style="margin:0 0 6px;color:#64748b;font-size:11px;font-weight:700;text-transform:uppercase;">Requirements and delivery details</p><p style="margin:0;white-space:pre-wrap;">{{ $sourcingRequest->notes ?: 'No additional details provided.' }}</p></div>
@endsection
@section('footer-note')Internal procurement notification. Follow up with the customer using the contact information above.@endsection
