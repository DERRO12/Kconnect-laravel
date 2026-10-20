{{-- Recursive tree of admin/manage-navigation (was displayTree()) --}}
                    <ul class="tree-root" id="sortable-{!! $parentId ? $parentId : 'root' !!}" data-parent="{!! $parentId ? $parentId : '' !!}">
                        @foreach ($items as $item)
                        @php
                            $hasChildren = isset($item['children']) && count($item['children']) > 0;
                            $statusClass = $item['status'] ? 'badge-active' : 'badge-inactive';
                            $statusText = $item['status'] ? 'Active' : 'Inactive';
@endphp
                            <li class="tree-item" data-id="{!! $item['id'] !!}" data-parent="{!! $parentId ? $parentId : '' !!}" data-sort="{!! $item['sort_order'] !!}">
                                <div class="tree-item-content">
                                    <div class="item-info">
                                        <div class="drag-handle" title="Drag to reorder">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>
                                        <div class="item-indent">
                                            @for ($i = 0; $i < $level; $i++)
                                                <span class="indent-line"></span>
                                            @endfor
                                        </div>
                                        <div class="item-icon {!! $item['is_label'] ? 'label' : '' !!}">
                                            <i class="fas {!! $item['is_label'] ? 'fa-tag' : 'fa-link' !!}"></i>
                                        </div>
                                        <div class="item-details">
                                            <span class="item-title">{{ $item['title'] }}</span>
                                            <span class="item-badge {!! $item['is_label'] ? 'badge-label' : 'badge-link' !!}">
                                                {!! $item['is_label'] ? 'LABEL' : 'LINK' !!}
                                            </span>
                                            <span class="item-badge {!! $statusClass !!}">
                                                {!! $statusText !!}
                                            </span>
                                            @if (!$item['is_label'] && !empty($item['url']))
                                                <div class="item-url-container">
                                                    <i class="fas fa-link text-muted"></i>
                                                    <span class="item-url" title="{{ $item['url'] }}">
                                                        @php 
                                                        $displayUrl = strlen($item['url']) > 40 ? substr($item['url'], 0, 40) . '...' : $item['url'];
                                                        echo htmlspecialchars($displayUrl);
@endphp
                                                    </span>
                                                    <button class="copy-url-btn" onclick="showFullUrl('{{ addslashes($item['url']) }}', event)" title="Show full URL">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="item-actions">
                                        <a href="#" 
                                           class="action-btn" 
                                           title="Edit"
                                           onclick="openEditModal({{ json_encode($item) }}); return false;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form method="POST" action="manage-navigation?action=toggle&id={!! $item['id'] !!}" class="d-inline"
                                              onsubmit="return confirm('Toggle status of this item?')">
                                            @csrf
                                            <button type="submit" class="action-btn toggle" title="Toggle Status">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="manage-navigation?action=delete&id={!! $item['id'] !!}" class="d-inline"
                                              onsubmit="return confirm('Delete this item? All child items will also be deleted!')">
                                            @csrf
                                            <button type="submit" class="action-btn delete" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                @if ($hasChildren)
                                    <div class="tree-children">
                                        @include('admin.manage-navigation-tree', ['items' => $item['children'], 'level' => $level + 1, 'parentId' => $item['id']])
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
