<?php

namespace App\Http\Controllers;


use App\Http\Filters\ReportFilter;
use App\Http\Requests\Report\FilterRequest;
use App\Models\Reports;

class ReportsController extends Controller
{
    public function index(FilterRequest $request)
    {
        $this->authorize('view', auth()->user());
        $data = $request->validated();
        $filter = app()->make(ReportFilter::class, ['queryParams' => array_filter($data)]);
        $query = Reports::filter($filter);

        if ($request->sort == null) {
            $sort = 'asc';
        }
        else
        {
            $sort = $request->sort;
        }
        switch($request->orderBy)
        {
            case 'total_count':
                $query->orderBy('total_count', $sort);
                break;
            case 'total_profit':
                $query->orderBy('total_profit', $sort);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sort);
                break;
        }
        return $query->paginate(10);
    }
}
