{{-- Recursive sub-department tree of admin/manage-departments (was displaySubDepartmentTree()) --}}
@if (count($subDeptsByParent[$parent_id] ?? []) > 0)
<div class="sub-departments">
@foreach ($subDeptsByParent[$parent_id] as $subDept)
@php
                                            $typeClass = strtolower($subDept['organization_type']);
@endphp
                                            <div class="sub-dept-item" onclick="viewDepartment({!! $subDept['id'] !!})">
                                                <span class="sub-dept-dot"></span>
                                                <div class="flex-grow-1">
                                                    <strong>{{ $subDept['name'] }}</strong>
                                                    @if ($subDept['code'])
                                                        <span class="badge bg-light text-dark ms-2">{!! $subDept['code'] !!}</span>
                                                    @endif
                                                    
                                                    @if ($subDept['head_name'])
                                                        <div class="head-info">
                                                            <i class="fas fa-user-tie"></i> Head: {!! $subDept['head_name'] !!}
                                                        </div>
                                                    @endif
                                                </div>
                                                <span class="badge {!! "badge-$typeClass" !!} me-2">
                                                    @php 
                                                    if ($subDept['organization_type'] == 'Sales') echo $subDept['sales_division'];
                                                    elseif ($subDept['organization_type'] == 'Support') echo $subDept['support_department'];
                                                    else echo 'Leadership';
@endphp
                                                </span>
                                                <div class="action-buttons">
                                                    <button class="action-btn btn-edit" onclick="event.stopPropagation(); openDepartmentModal({!! $subDept['id'] !!})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="action-btn btn-employees" onclick="event.stopPropagation(); window.location.href='orgchart_manager'">
                                                        <i class="fas fa-users"></i>
                                                    </button>
                                                    <button class="action-btn btn-delete" onclick="event.stopPropagation(); deleteDepartment({!! $subDept['id'] !!})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
@include('admin.manage-departments-subtree', ['parent_id' => $subDept['id']])
@endforeach
</div>
@endif
