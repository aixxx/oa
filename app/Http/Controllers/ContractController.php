<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $contracts = Contract::where('applicant', auth()->id())->orderBy('check_at', 'desc')->orderBy('apply_at','desc')->paginate(15);
        $contracts = Contract::formatData($contracts);
        $contracts->each(function ($item, $key) {
            if ($item->parties) {
                $item->parties = implode(',', collect($item->parties)->pluck(2)->toArray());
            }
        });
        $businessTypes = Contract::$businessType;
        $statusTypes   = Contract::$status;
        return view('contracts.index', compact('contracts', 'businessTypes', 'statusTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function show(Contract $contract, $id)
    {
        //
        $contract = Contract::with('user')->findOrFail($id);
        $contract = Contract::formatData($contract);
        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Contract $contract)
    {
        //
        $contract = Contract::with('user')->findOrFail($id);
        $contract = Contract::formatData($contract);
        return view('contracts.edit', compact('contract'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, Contract $contract)
    {
        //
        $updateData             = [];
        $contract               = Contract::findOrFail($id);
        $requestData            = $request->all();
        $updateData['scan_id']  = (isset($requestData['ids'][0]) && $requestData['ids'][0]) ? intval($requestData['ids'][0]) : 0;
        $updateData['start_at'] = (isset($requestData['start_at']) && $requestData['start_at']) ? $requestData['start_at'] : "";
        $updateData['end_at']   = (isset($requestData['end_at']) && $requestData['end_at']) ? $requestData['end_at'] : "";
        $updateData['status']   = Contract::CONTRACT_STATUS_HAS_BEEN_ARCHIVED;
        if ($contract->update($updateData)) {
            return response()->json(['status' => 'success', 'message' => "归档成功"]);
        } else {
            return response()->json(['status' => 'fail', 'message' => '归档失败']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contract $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contract $contract)
    {
        //
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        $where       = [];
        $requestData = $request->except('_token');
        $exceptPage  = $request->except('_token', 'page');
        if (isset($requestData['number']) && $requestData['number']) {
            $where[] = ['number', 'like', '%' . $requestData['number'] . '%'];
        }

        if (isset($requestData['name']) && $requestData['name']) {
            $where[] = ['name', 'like', '%' . $requestData['name'] . '%'];
        }

        if (isset($requestData['business_type']) && $requestData['business_type']) {
            $where[] = ['business_type', '=', $requestData['business_type']];
        }

        if (isset($requestData['parties']) && $requestData['parties']) {
            $where[] = ['parties', 'like', '%' . $requestData['parties'] . '%'];
        }

        if (isset($requestData['start_at'][0]) && $requestData['start_at'][0]) {
            $where[] = ['start_at', '>=', $requestData['start_at'][0]];
        }

        if (isset($requestData['start_at'][1]) && $requestData['start_at'][1]) {
            $where[] = ['start_at', '<=', $requestData['start_at'][1]];
        }

        if (isset($requestData['end_at'][0]) && $requestData['end_at'][0]) {
            $where[] = ['end_at', '>=', $requestData['end_at'][0]];
        }

        if (isset($requestData['end_at'][1]) && $requestData['end_at'][1]) {
            $where[] = ['end_at', '<=', $requestData['end_at'][1]];
        }

        if (isset($requestData['amount'][0]) && $requestData['amount'][0]) {
            $where[] = ['amount', '>=', $requestData['amount'][0]];
        }

        if (isset($requestData['amount'][1]) && $requestData['amount'][1]) {
            $where[] = ['amount', '<=', $requestData['amount'][1]];
        }

        if (isset($requestData['status']) && $requestData['status']) {
            $where[] = ['status', '=', $requestData['status']];
        }

        if ($where) {
            $contracts = Contract::where($where)->paginate(15);
        } else {
            $contracts = Contract::paginate(15);
        }

        if ($contracts->isNotEmpty()) {
            $contracts = Contract::formatData($contracts);
        }

        $contracts->each(function ($item, $key) {
            if ($item->parties) {
                $item->parties = implode(',', collect($item->parties)->pluck(2)->toArray());
            }
        });
        $businessTypes = Contract::$businessType;
        $statusTypes   = Contract::$status;
        if ($exceptPage) {
            $requestData = $this->validDataIsNull($requestData) ? $requestData : [];
        }

        return view('contracts.index', compact('contracts', 'businessTypes', 'statusTypes', 'requestData'));
    }


    public function validDataIsNull($data)
    {
        $flag = true;
        if (($data['number'] == null) && ($data['name'] == null) && ($data['business_type'] == null) &&
            ($data['parties'] == null) && ($data['start_at'][0] == null) && ($data['start_at'][1] == null) &&
            ($data['end_at'][0] == null) && ($data['end_at'][1] == null) && ($data['amount'][0] == null) &&
            ($data['amount'][1] == null) && ($data['status'] == null)) {
            $flag = false;
        }

        return $flag;
    }
}
