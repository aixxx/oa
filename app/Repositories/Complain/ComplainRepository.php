<?php

namespace App\Repositories\Complain;

use App\Repositories\EntryRepository;
use Illuminate\Http\Request;
use App\Models\Workflow\Entry;
use App\Models\Entry\EntryType;

class ComplainRepository extends EntryRepository
{
    public function showForm()
    {
        return parent::showTemplate(Entry::WORK_FLOW_NO_SALARY_COMPLAIN);
    }

    public function storeWorkflow(Request $request)
    {
        $entryId = parent::updateFlow($request, 0);

        $data = [
            'entry_id' => $entryId,
            'type_key' => EntryType::ENTRY_RELATION_TYPE_SALARY,
            'type_id_value' => $request->get('salary_id', 100)
        ];

        (new EntryType())->fill($data)->save();
        return $this->returnApiJson();
    }

    public function workflowShow(Request $request)
    {

    }

    public function workflowAuthorityShow(Request $request)
    {

    }
}