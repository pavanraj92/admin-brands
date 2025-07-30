<?php

namespace admin\brands\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use admin\brands\Requests\BrandCreateRequest;
use admin\brands\Requests\BrandUpdateRequest;
use admin\brands\Models\Brand;

class BrandManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('admincan_permission:brands_manager_list')->only(['index']);
        $this->middleware('admincan_permission:brands_manager_create')->only(['create', 'store']);
        $this->middleware('admincan_permission:brands_manager_edit')->only(['edit', 'update']);
        $this->middleware('admincan_permission:brands_manager_view')->only(['show']);
        $this->middleware('admincan_permission:brands_manager_delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        try {
            $brands = Brand::filter($request->query('keyword'))
                ->filterByStatus($request->query('status'))
                ->sortable()
                ->latest()
                ->paginate(Brand::getPerPageLimit())
                ->withQueryString();

            return view('brand::admin.index', compact('brands'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load brands: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('brand::admin.createOrEdit');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load brands: ' . $e->getMessage());
        }
    }

    public function store(BrandCreateRequest $request)
    {
        try {
            $requestData = $request->validated();

            Brand::create($requestData);
            return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load brands: ' . $e->getMessage());
        }
    }

    /**
     * show brand details
     */
    public function show(Brand $brand)
    {
        try {
            return view('brand::admin.show', compact('brand'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load brands: ' . $e->getMessage());
        }
    }

    public function edit(Brand $brand)
    {
        try {
            return view('brand::admin.createOrEdit', compact('brand'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load brand for editing: ' . $e->getMessage());
        }
    }

    public function update(BrandUpdateRequest $request, Brand $brand)
    {
        try {
            $requestData = $request->validated();

            $brand->update($requestData);
            return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load brand for editing: ' . $e->getMessage());
        }
    }

    public function destroy(Brand $brand)
    {
        try {
            $brand->delete();
            return response()->json(['success' => true, 'message' => 'Record deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete record.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $brand = Brand::findOrFail($request->id);
            $brand->status = $request->status;
            $brand->save();

            // create status html dynamically        
            $dataStatus = $brand->status == '1' ? '0' : '1';
            $label = $brand->status == '1' ? 'Active' : 'InActive';
            $btnClass = $brand->status == '1' ? 'btn-success' : 'btn-warning';
            $tooltip = $brand->status == '1' ? 'Click to change status to inactive' : 'Click to change status to active';

            $strHtml = '<a href="javascript:void(0)"'
                . ' data-toggle="tooltip"'
                . ' data-placement="top"'
                . ' title="' . $tooltip . '"'
                . ' data-url="' . route('admin.brands.updateStatus') . '"'
                . ' data-method="POST"'
                . ' data-status="' . $dataStatus . '"'
                . ' data-id="' . $brand->id . '"'
                . ' class="btn ' . $btnClass . ' btn-sm update-status">' . $label . '</a>';

            return response()->json(['success' => true, 'message' => 'Status updated to ' . $label, 'strHtml' => $strHtml]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete record.', 'error' => $e->getMessage()], 500);
        }
    }
}
