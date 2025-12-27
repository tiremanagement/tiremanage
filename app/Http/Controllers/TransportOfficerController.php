<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TireRequest;
use App\Models\Approval;
use App\Models\Supplier;
use App\Models\Receipt;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReceiptMail;

class TransportOfficerController extends Controller
{
    public function __construct()
    {
        // Only Transport Officer access
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || !$user->role || strtolower($user->role->name) !== 'transport officer') {
                abort(403, 'Access restricted to Transport Officer.');
            }
            return $next($request);
        });
    }

    /** ---------------- PENDING REQUESTS ---------------- */
    public function pending()
    {
        $pendingRequests = TireRequest::where('status', Approval::STATUS_PENDING_TRANSPORT)
            ->where('current_level', Approval::LEVEL_TRANSPORT_OFFICER)
            ->with(['user', 'driver', 'vehicle', 'tire'])
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.transport_officer.pending', compact('pendingRequests'));
    }

    /** ---------------- APPROVED REQUESTS ---------------- */
public function approved()
{
    $approvedRequests = TireRequest::where('status', Approval::STATUS_APPROVED_BY_TRANSPORT)
        ->with(['user', 'driver', 'vehicle', 'tire'])
        ->orderByDesc('updated_at')
        ->get();

    $suppliers = Supplier::all();
    // Distinct list of towns that have registered suppliers
    $towns = Supplier::whereNotNull('town')
        ->where('town', '!=', '')
        ->distinct()
        ->orderBy('town')
        ->pluck('town');

    return view('dashboard.transport_officer.approved', compact('approvedRequests', 'suppliers', 'towns'));
}


    /** ---------------- REJECTED REQUESTS ---------------- */
    public function rejected()
    {
        $rejectedRequests = TireRequest::where('status', Approval::STATUS_REJECTED_BY_TRANSPORT)
            ->with(['user', 'driver', 'vehicle', 'tire'])
            ->orderByDesc('updated_at')
            ->get();

        return view('dashboard.transport_officer.rejected', compact('rejectedRequests'));
    }

    /** ---------------- EDIT REQUEST ---------------- */
    public function edit($id)
    {
        $requestItem = TireRequest::with(['user', 'vehicle', 'tire', 'approvals'])->findOrFail($id);
        return view('dashboard.transport_officer.edit_request', compact('requestItem'));
    }

    /** ---------------- UPDATE REQUEST ---------------- */
    public function update(Request $request, $id)
    {
        $requestItem = TireRequest::findOrFail($id);

        $requestItem->update([
            'damage_description' => $request->damage_description,
        ]);

        $status = match ($request->status) {
            'approved' => Approval::STATUS_APPROVED_BY_TRANSPORT,
            'rejected' => Approval::STATUS_REJECTED_BY_TRANSPORT,
            default => Approval::STATUS_PENDING_TRANSPORT,
        };

        Approval::updateOrCreate(
            ['request_id' => $requestItem->id, 'level' => Approval::LEVEL_TRANSPORT_OFFICER],
            [
                'remarks' => $request->remarks,
                'approved_by' => auth()->id(),
                'status' => $status,
            ]
        );

        if ($status === Approval::STATUS_APPROVED_BY_TRANSPORT) {
            $requestItem->update([
                'status' => Approval::STATUS_APPROVED_BY_TRANSPORT,
                'current_level' => Approval::LEVEL_FINISHED,
            ]);
        return redirect()->route('transport_officer.approved')
                 ->with('success', 'Receipt sent successfully!');

        }

        if ($status === Approval::STATUS_REJECTED_BY_TRANSPORT) {
            $requestItem->update([
                'status' => Approval::STATUS_REJECTED_BY_TRANSPORT,
                'current_level' => Approval::LEVEL_TRANSPORT_OFFICER,
            ]);
            return redirect()->route('transport_officer.rejected')
                ->with('error', 'Supplier contact or email not found. Message not sent.');
        }

        // Pending
        $requestItem->update([
            'status' => Approval::STATUS_PENDING_TRANSPORT,
            'current_level' => Approval::LEVEL_TRANSPORT_OFFICER,
        ]);

        return redirect()->route('transport_officer.pending')
            ->with('success', '⏳ Request updated and remains pending.');
    }

    /** ---------------- APPROVE QUICK ACTION ---------------- */
    public function approve($id)
    {
        $req = TireRequest::findOrFail($id);

        $req->update([
            'status' => Approval::STATUS_APPROVED_BY_TRANSPORT,
            'current_level' => Approval::LEVEL_FINISHED,
        ]);

        Approval::updateOrCreate(
            ['request_id' => $req->id, 'level' => Approval::LEVEL_TRANSPORT_OFFICER],
            [
                'approved_by' => Auth::id(),
                'status' => Approval::STATUS_APPROVED_BY_TRANSPORT,
            ]
        );

        return redirect()->route('transport_officer.approved')
            ->with('success', '✅ Request approved successfully.');
    }

    /** ---------------- REJECT QUICK ACTION ---------------- */
    public function reject(Request $request, $id)
    {
        $req = TireRequest::findOrFail($id);

        // Build rejection remarks from reason and custom_reason
        $remarksParts = [];
        if ($request->input('reason')) {
            $remarksParts[] = $request->input('reason');
        }
        if ($request->input('custom_reason')) {
            $remarksParts[] = $request->input('custom_reason');
        }
        $remarks = implode(': ', $remarksParts);

        $req->update([
            'status' => Approval::STATUS_REJECTED_BY_TRANSPORT,
            'current_level' => Approval::LEVEL_TRANSPORT_OFFICER,
        ]);

        Approval::updateOrCreate(
            ['request_id' => $req->id, 'level' => Approval::LEVEL_TRANSPORT_OFFICER],
            [
                'approved_by' => Auth::id(),
                'status' => Approval::STATUS_REJECTED_BY_TRANSPORT,
                'remarks' => $remarks,
            ]
        );

        return redirect()->route('transport_officer.rejected')
            ->with('success', '✅ Request rejected successfully.');
    }


public function createReceipt($id)
{
    // load request with relations
    $tireRequest = TireRequest::with(['user', 'driver', 'vehicle', 'tire'])->findOrFail($id);
    $suppliers = Supplier::all();

    // If you already open the inline form in approved.blade.php you can keep this view or ignore it.
    return view('dashboard.transport_officer.create_receipt', compact('tireRequest', 'suppliers'));
}

public function storeReceipt(Request $request)
{
    $tireRequestModel = new TireRequest();
    $supplierModel = new Supplier();

    $validated = $request->validate([
        'request_id'  => ['required', Rule::exists($tireRequestModel->getTable(), 'id')],
        'supplier_id' => ['required', Rule::exists($supplierModel->getTable(), 'id')],
        'amount'      => 'required|numeric',
        'description' => 'nullable|string',
    ]);

    $tireRequest = TireRequest::with(['user', 'vehicle'])->findOrFail($validated['request_id']);
    $supplier = Supplier::findOrFail($validated['supplier_id']);

    // Town restriction removed: any supplier can be chosen regardless of town

    // Create receipt record
    $receipt = Receipt::create([
        'request_id'  => $tireRequest->id,
        'user_id'     => $tireRequest->user_id,
        'supplier_id' => $supplier->id,
        'amount'      => $validated['amount'],
        'description' => $validated['description'] ?? null,
        'is_read'      => false,
    ]);

    try {
        $receipt->issued_date = now();
        $receipt->status = 'issued';
        $receipt->save();
    } catch (\Throwable $e) {
        // ignore if column doesn't exist
    }

    // Update tire request + approval table
    $tireRequest->update([
        'status' => Approval::STATUS_APPROVED_BY_TRANSPORT,
        'current_level' => Approval::LEVEL_FINISHED,
    ]);

    Approval::updateOrCreate(
        ['request_id' => $tireRequest->id, 'level' => Approval::LEVEL_TRANSPORT_OFFICER],
        [
            'approved_by' => auth()->id(),
            'status' => Approval::STATUS_APPROVED_BY_TRANSPORT,
            'remarks' => 'Receipt sent to supplier ' . $supplier->name,
        ]
    );

    // Prepare email preview (do NOT auto-send) — user clicks Send button in modal
    $driverEmail   = $tireRequest->user->email ?? null;
    $supplierEmail = $supplier->email ?? null;

    // Render email HTML preview
    $previewHtml = view('emails.receipt', compact('receipt'))->render();

    // Store preview info in session for modal with CC/BCC fields
    $preview = [
        'subject' => 'Tire Receipt #' . $receipt->id,
        'html' => $previewHtml,
        'to' => $supplierEmail ?? '',
        'cc' => $driverEmail ?? '',
        'bcc' => '',
        'receipt_id' => $receipt->id,
    ];

    return redirect()->route('transport_officer.approved')
        ->with('success', 'Receipt generated. Preview ready.')
        ->with('email_preview', $preview);
}

/**
 * Send the receipt email with user-specified recipients (TO, CC, BCC).
 */
public function sendReceiptEmail(Request $request)
{
    $data = $request->validate([
        'receipt_id' => ['required', 'exists:receipts,id'],
        'to' => 'required|email',
        'cc' => 'nullable|email',
        'bcc' => 'nullable|email',
    ]);

    $receipt = Receipt::with(['supplier', 'tireRequest.user', 'tireRequest.vehicle', 'tireRequest.tire'])->findOrFail($data['receipt_id']);

    try {
        $mail = Mail::to($data['to']);
        
        if (!empty($data['cc'])) {
            $mail->cc($data['cc']);
        }
        if (!empty($data['bcc'])) {
            $mail->bcc($data['bcc']);
        }
        
        $mail->send(new ReceiptMail($receipt));
    } catch (\Throwable $e) {
        return redirect()->route('transport_officer.approved')
            ->with('error', 'Failed to send email: ' . $e->getMessage());
    }

    // mark receipt status
    $receipt->status = 'emailed';
    $receipt->save();

    return redirect()->route('transport_officer.approved')
        ->with('success', 'Receipt email sent successfully.');
}


public function generateReceiptForDriver($requestId)
{
    $tireRequest = TireRequest::with(['user','vehicle'])->findOrFail($requestId);
    $receipt = Receipt::create([
        'request_id' => $tireRequest->id,
        'user_id'    => $tireRequest->user_id,
        'supplier_id'=> null,
        'amount'     => 0,
        'description'=> 'Generated for driver',
    ]);
    $receipt->issued_date = now();
    $receipt->status = 'issued';
    $receipt->save();

    return redirect()->back()->with('success', 'Receipt generated for driver.');
}

/**
 * Normalize supplier contact to international digits for wa.me links (no + sign).
 * Examples:
 *   "+94711234567" -> "94711234567"
 *   "0711234567"   -> "94711234567" (uses DEFAULT_PHONE_COUNTRY)
 */
private function normalizePhoneForWhatsApp(string $rawPhone): string
{
    // remove everything except digits and plus
    $phone = preg_replace('/[^0-9+]/', '', (string)$rawPhone);

    $defaultCountry = config('app.default_phone_country', env('DEFAULT_PHONE_COUNTRY', '94'));

    if (strpos($phone, '+') === 0) {
        return ltrim($phone, '+');
    }

    // remove leading zero(s)
    $phone = preg_replace('/^0+/', '', $phone);

    // If phone looks short (no country code) prepend default country code
    if (strlen($phone) <= 9) {
        $phone = $defaultCountry . $phone;
    }

    return $phone;
}

}


